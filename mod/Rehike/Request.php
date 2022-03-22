<?php
namespace Rehike;

use YukisCoffee\CoffeeRequest\CoffeeRequest;

class Request
{
    // Constant namespace identifiers
    // These are used for on response callbacks
    // for different request types.
    const NS_INNERTUBE = "NS_INNERTUBE";
    const NS_URL = "NS_URL";
    const NS_INITIALDATA = "NS_INITIALDATA";

    public static $RequestManager;

    public static $requests = [];

    public static function init()
    {
        self::$RequestManager = new CoffeeRequest();
        self::$RequestManager->requestsMaxAttempts = 1;
    }

    public static $innertubeRequests = [];

    public static function urlRequest($id, $url, $namespace = self::NS_URL)
    {
        $namespacedId = "{$namespace}_{$id}";

        $host = "https://www.youtube.com";

        // Convert relative links
        if (0 == strpos($url, "/")) $url = $host . $url;

        // Remember status for callback mode
        self::$RequestManager->queueRequest($url, [], $namespacedId);
        self::$requests += [$id => $namespace];
    }

    public static function initialDataRequest($id, $url)
    {
        return self::urlRequest($id, $url, self::NS_INITIALDATA);
    }

    public static function innertubeRequest($id, $action, $body = null, $cname = "WEB", $cver = "2.20220303.01.01")
    {
        $namespace = self::NS_INNERTUBE;
        $namespacedId = "{$namespace}_{$id}";

        $host = "https://www.youtube.com";
        $key = "AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8";
        // Fucking cursed
        $body = (object)((array)$body + (array)InnertubeContext::generate($cname, $cver));

        self::$RequestManager->queueRequest(
            "{$host}/youtubei/v1/{$action}?key={$key}",
            [
                "headers" => [
                    "Content-Type" => "application/json",
                    "x-goog-visitor-id" => InnertubeContext::genVisitorData(ContextManager::$visitorData)
                ],
                "post" => true,
                "body" => json_encode($body)
            ],
            $namespacedId
        );
        
        // Remember status for callback mode
        self::$requests += [$id => $namespace];
    }

    public static function handleInnertubeResponse($response)
    {
        return $response;
    }
    
    public static function handleInitialDataResponse($response)
    {
        preg_match("/var ytInitialData = ({.*)?;</", $response, $matches);
        if ($matches[1])
        {
            return $matches[1];
        }
    }

    public static function getResponses()
    {
        $responses = self::$RequestManager->runQueue();
        $final = [];

        // Find namespace for handling
        foreach (self::$requests as $id => $namespace)
        {
            $me = @$responses["{$namespace}_{$id}"];
            if (!isset($me)) continue; // assume errored

            switch ($namespace)
            {
                case self::NS_INNERTUBE:
                    $final += [$id => self::handleInnertubeResponse($me)];
                    break;
                case self::NS_URL:
                    $final += [$id => $me];
                    break;
                case self::NS_INITIALDATA:
                    $final += [$id => self::handleInitialDataResponse($me)];
                    break;
            }
        }

        return $final;
    }
}
Request::init();