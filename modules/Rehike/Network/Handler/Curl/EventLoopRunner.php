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

if (PHP_VERSION_ID >= 81000)
{

//=====================================================================
// PHP 8.1+ implementation, leverages Fibers
//=====================================================================
trait EventLoopRunner // implements Event::onRun()
{

    private array $normalItems = [];
    private array $fiberItems = [];

    #[Override]
    public function onRun(): Generator/*<void>*/
    {
        // Defined in CurlHandler
        $requests = &$this->requests;

        $normalItems = [];
        $fiberItems = [];

        $halfOfList = floor(count($this->requests) / 2);

        if (count($requests) == 0)
        {
            $this->fulfil();
            return;
        }

        $mhFiber = curl_multi_init();
        $mhNormal = curl_multi_init();
        $codesMap = [];

        // Register all queued requests in the handle array
        foreach ($requests as $index => $request)
        {
            $index > $halfOfList
                ? curl_multi_add_handle($mhFiber, $request->handle)
                : curl_multi_add_handle($mhNormal, $request->handle);
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
        $fiber->start();

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
            }
        }
        while ($active && CURLM_OK == $status);

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

        $this->fulfill();
    }

} // EventLoopRunner

} // PHP_VERSION_ID >= 81000