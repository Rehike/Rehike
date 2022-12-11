<?php
namespace YukisCoffee\CoffeeRequest\Handler\Curl;

use YukisCoffee\CoffeeRequest\Attributes\Override;
use YukisCoffee\CoffeeRequest\Attributes\RequireMember;

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

if (false/*PHP_VERSION_ID >= 81000*/)
{

//=====================================================================
// PHP 8.1+ implementation, leverages Fibers
//=====================================================================
#[RequireMember("requests")]
#[RequireMember("makeResponse(int, string)")]
#[RequireMember("sendResponse(Request, Response)")]
trait EventLoopRunner // implements Event::onRun()
{

    private array $normalItems = [];
    private array $fiberItems = [];

    #[Override]
    public function onRun(): Generator/*<void>*/
    {
        yield;
    }

} // EventLoopRunner

}
else
{

//=====================================================================
// PHP 7 and 8.0 implementation
//=====================================================================
#[RequireMember("requests")]
#[RequireMember("makeResponse(int, string)")]
#[RequireMember("sendResponse(Request, Response)")]
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

        // Register all queued requests in the handle array
        foreach ($requests as $request)
        {
            curl_multi_add_handle($mh, $request->handle);
        }

        do
        {
            $status = curl_multi_exec($mh, $active);

            if ($active)
            {
                usleep(1);
                yield;
            }
        }
        while ($active && CURLM_OK == $status);

        // Report each of the responses.
        foreach ($requests as $request)
        {
            $response = $this->makeResponse(
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