<?php
namespace Rehike\Network\Handler\Curl;

use Rehike\Attributes\Override;

use const PHP_VERSION_ID;
use Generator;
use Fiber; // PHP 8.1
use function usleep;

// cURL imports:
use function curl_multi_init;
use function curl_multi_add_handle;
use function curl_multi_exec;
use function curl_multi_select;
use function curl_multi_getcontent;
use function curl_multi_remove_handle;
use function curl_multi_close;
use function curl_getinfo;
use function curl_close;
use const CURLM_OK;
use const CURLINFO_HTTP_CODE;
use CurlMultiHandle;
use Rehike\Async\Promise\PromiseStatus;

if (PHP_VERSION_ID >= 80100)
{

//=====================================================================
// PHP 8.1+ implementation, leverages Fibers
//=====================================================================
trait EventLoopRunner // implements Event::onRun()
{

    #[Override]
    public function onRun(): Generator/*<void>*/
    {
        // Defined in CurlHandler
        $requests = &$this->requests;

        $halfOfList = floor(count($this->requests) / 2);

        if (count($requests) == 0)
        {
            $this->fulfil();
            return;
        }
        
        $knownRequests = [];

        $mhFiber = curl_multi_init();
        $mhNormal = curl_multi_init();
        $codesMap = [];

        // Register all queued requests in the handle array
        foreach ($requests as $index => $request)
        {
            $index > $halfOfList
                ? curl_multi_add_handle($mhFiber, $request->handle)
                : curl_multi_add_handle($mhNormal, $request->handle);

            $knownRequests[] = $request;
        }

        // Initialize fiber:
        $fiber = new Fiber(function(CurlMultiHandle $mh) {
            $active = null;

            do
            {
                curl_multi_exec($mh, $active);
                $info = curl_multi_info_read($mh);
                if ($info)
                    $codesMap[(int)$info["handle"]] = $info["result"];
                curl_multi_select($mh);
                Fiber::suspend();
            }
            while ($active);
        });
        $fiber->start($mhFiber);
        
        // We've already handled all existing requests, so clear the dirty flag.
        $this->requestsDirty = false;

        do
        {
            $status = curl_multi_exec($mhNormal, $active);
            $info = curl_multi_info_read($mhNormal);
            if ($info)
                $codesMap[(int)$info["handle"]] = $info["result"];

            if ($active)
            {
                if (-1 == curl_multi_select($mhNormal))
                {
                    usleep(10);
                }
                
                yield;
                
                // Now we're resuming. If some more requests came through,
                // we need to add them to the active stream now.
                if ($this->requestsDirty)
                {
                    // Since our list can grow or shrink, we will now recompute the
                    // half of list variable. We'll get a good average from our starting
                    // value and the would-be starting value if we had this many to begin
                    // with, since we obviously can't move between handlers.
                    $halfOfList = floor($halfOfList + floor(count($this->requests) / 2) / 2);
                    
                    foreach ($requests as $index => $request)
                    {
                        if (in_array($request, $knownRequests))
                        {
                            continue;
                        }
                        
                        $index > $halfOfList
                            ? curl_multi_add_handle($mhFiber, $request->handle)
                            : curl_multi_add_handle($mhNormal, $request->handle);

                        $knownRequests[] = $request;
                    }
                    
                    $this->requestsDirty = false;
                }
            }
        }
        while ($active && CURLM_OK == $status);
        
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! //
        //                *** END OF EVENT LOOP AREA ***                     //
        //         The code should never "yield" past this point.            //
        //        All code here must be synchronous as we will be            //
        //                   processing responses.                           //
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! //

        // Close fiber:
        do
        {
            $fiber->resume();
        }
        while (!$fiber->isTerminated());

        // Report each of the responses.
        foreach ($requests as $index => $request)
        {
            $code = isset($codesMap[(int)$request->handle])
                ? $codesMap[(int)$request->handle]
                : 0;

            $response = $this->makeResponse(
                $code,
                curl_getinfo($request->handle, CURLINFO_HTTP_CODE),
                curl_multi_getcontent($request->handle),
                $request
            );

            $this->sendResponse($request->instance, $response);

            $index > $halfOfList
                ? curl_multi_remove_handle($mhFiber, $request->handle)
                : curl_multi_remove_handle($mhNormal, $request->handle);

            curl_close($request->handle);
        }

        curl_multi_close($mhNormal);
        curl_multi_close($mhFiber);
        
        foreach ($this->requests as $request)
        {
            if ($request->instance->getPromise()->status == PromiseStatus::PENDING)
            {
                // XXX(isabella): New requests can STILL be added before we fulfill,
                // i.e. from a synchronous Promise::then() callback in sendResponse()
                // when deferred promises are disabled in the async library. In this
                // case, we will have to reset the network handler without clearing
                // requests and let them run like normal.
                $this->restartManager();
                return;
            }
        }

        $this->fulfill();
    }

} // EventLoopRunner

}
else
{

//=====================================================================
// PHP 8.0 implementation
//=====================================================================
trait EventLoopRunner // implements Event::onRun()
{

    #[Override]
    public function onRun(): Generator/*<void>*/
    {
        // Defined in CurlHandler
        $requests = &$this->requests;
        
        $knownRequests = [];

        if (count($requests) == 0)
        {
            $this->fulfil();
            return;
        }

        $mh = curl_multi_init();
        $codesMap = [];

        // Register all queued requests in the handle array
        foreach ($requests as $request)
        {
            curl_multi_add_handle($mh, $request->handle);
            $knownRequests[] = $request;
            $codesMap[(int)$request->handle] = 0;
        }

        do
        {
            $status = curl_multi_exec($mh, $active);
            $info = curl_multi_info_read($mh);
            if ($info)
                $codesMap[(int)$info["handle"]] = $info["result"];

            if ($active)
            {
                // This seems to work better than the previous implementation, which was just
                // a usleep(1) and seems to have errored somewhat.
                if (-1 == curl_multi_select($mh))
                {
                    usleep(10);
                }

                yield;
                
                // Now we're resuming. If some more requests came through,
                // we need to add them to the active stream now.
                if ($this->requestsDirty)
                {
                    foreach ($requests as $request)
                    {
                        if (in_array($request, $knownRequests))
                        {
                            continue;
                        }
                        
                        curl_multi_add_handle($mh, $request->handle);
                        $knownRequests[] = $request;
                    }
                    
                    $this->requestsDirty = false;
                }
            }
        }
        while ($active && CURLM_OK == $status);

        // Report each of the responses.
        foreach ($requests as $request)
        {
            $code = isset($codesMap[(int)$request->handle])
                ? $codesMap[(int)$request->handle]
                : 0;

            $response = $this->makeResponse(
                $code,
                curl_getinfo($request->handle, CURLINFO_HTTP_CODE),
                curl_multi_getcontent($request->handle),
                $request
            );

            $this->sendResponse($request->instance, $response);

            curl_multi_remove_handle($mh, $request->handle);

            curl_close($request->handle);
        }

        curl_multi_close($mh);
        
        foreach ($this->requests as $request)
        {
            if ($request->instance->getPromise()->status == PromiseStatus::PENDING)
            {
                // XXX(isabella): New requests can STILL be added before we fulfill,
                // i.e. from a synchronous Promise::then() callback in sendResponse()
                // when deferred promises are disabled in the async library. In this
                // case, we will have to reset the network handler without clearing
                // requests and let them run like normal.
                $this->restartManager();
                return;
            }
        }

        $this->fulfill();
    }

} // EventLoopRunner

} // PHP_VERSION_ID >= 80100