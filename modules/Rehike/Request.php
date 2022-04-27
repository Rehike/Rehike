<?php
namespace Rehike;

use YukisCoffee\CoffeeRequest\CoffeeRequest;

class Request
{
    public static $innertubeRequests = [];

    public static function innertubeRequest($id, $action, $body = null, $cname = "WEB", $cver = "2.20220303.01.01")
    {
        $host = "https://www.youtube.com";
        $key = "AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8";
        // Fucking cursed
        $body = (object)((array)$body + (array)InnertubeContext::generate($cname, $cver));

        CoffeeRequest::queueRequest(
            "{$host}/youtubei/v1/{$action}?key={$key}",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                    "x-goog-visitor-id" => InnertubeContext::genVisitorData(ContextManager::$visitorData)
                ],
                "post" => true,
                "body" => json_encode($body)
            ],
            $id
        );
        
        self::$innertubeRequests = CoffeeRequest::$requestQueue;
    }

    public static function getInnertubeResponses()
    {
        $store = CoffeeRequest::$requestQueue;
        CoffeeRequest::$requestQueue = self::$innertubeRequests;
        $response = CoffeeRequest::runQueue();
        CoffeeRequest::$requestQueue = $store;
        return $response;
    }
}