<?php
namespace Rehike\RequestTypes;

use Rehike\Request;
use Rehike\InnertubeContext;
use Rehike\ContextManager;

/**
 * Implements the behaviour needed to request the InnerTube API.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
trait InnertubeRequest
{
    /**
     * Add an InnerTube request to the queue.
     * 
     * @param string $id for the request queue
     * @param string $action to use (part after v1 in the URL)
     * @param object $body to submit
     * @param string $cname (client name)
     * @param string $cver (client version)
     */
    public static function queueInnertubeRequest($id, $action, $body = null, $cname = "WEB", $cver = "2.20221104.02.00")
    {
        $host = "https://www.youtube.com";
        $key = "AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8";
        // Fucking cursed
        $body = (object)((array)$body + (array)InnertubeContext::generate($cname, $cver));

        Request::queueRequest(
            "{$host}/youtubei/v1/{$action}?key={$key}",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                    "x-goog-visitor-id" => InnertubeContext::genVisitorData(ContextManager::$visitorData)
                ] + Request::$innertubeHeaders,
                "post" => true,
                "body" => json_encode($body)
            ],
            Request::NS_INNERTUBE,
            $id
        );
    }

    /**
     * Perform a single InnerTube request.
     * 
     * @param string $action to use (part after v1 in the URL)
     * @param object $body to submit
     * @param string $cname (client name)
     * @param string $cver (client version)
     * @return string
     */
    public static function innertubeRequest($action, $body = null, $cname = "WEB", $cver = "2.20220303.01.01")
    {
        return Request::singleRequestWrapper(function() use ($action, $body, $cname, $cver) {
            self::queueInnertubeRequest("singleRequest", $action, $body, $cname, $cver);
        });
    }
}