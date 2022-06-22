<?php
namespace Rehike;

use YukisCoffee\CoffeeRequest\CoffeeRequest;

/**
 * Implement the Request manager.
 * 
 * This adds onto CoffeeRequest behaviour and manages all
 * general requests.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Request
{
    /**
     * Array of current InnerTube requests.
     * 
     * @var array
     */
    public static $innertubeRequests = [];

    /**
     * Perform an InnerTube request.
     * 
     * @param string $id to assign the request
     * @param string $action to take (after v1 in the URL)
     * @param string|null $body to pass
     * @param string|int $cname (client name) enum or index
     * @param string $cver (client version) number
     * 
     * @return void
     */
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

    /**
     * Get all relevant InnerTube responses.
     * 
     * @return mixed
     */
    public static function getInnertubeResponses()
    {
        $store = CoffeeRequest::$requestQueue;
        CoffeeRequest::$requestQueue = self::$innertubeRequests;
        $response = CoffeeRequest::runQueue();
        CoffeeRequest::$requestQueue = $store;
        return $response;
    }
}