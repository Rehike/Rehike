<?php
namespace Rehike;

use Rehike\Ds\EasyAccessMap;

/**
 * Utilities for querying information about the request session.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RequestSession
{
    /**
     * Gets the name of the current server.
     */
    public static function getServerName(): string
    {
        $runtimeInfo = new RuntimeInfo();
        return $runtimeInfo->serverVersion;
    }
    
    /**
     * Get all requested headers.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     */
    public static function getRequestHeaders(): EasyAccessMap
    {
        // Apache (only) has apache_request_headers
        // function:
        if (function_exists("\apache_request_headers"))
        {
            $headers = [];

            // Convert all elements to lowercase
            foreach (\apache_request_headers() as $key => $value)
            {
                $headers += [strtolower($key) => $value];
            }


            /**
             * Friendly API for accessing HTTP headers.
             * 
             * This converts header names to camelCase at
             * access time, allowing them to be accessed with
             * simple variable names. It can also be accessed like an
             * array.
             * 
             * For example:
             * 
             *      Content-Type == contentType
             */
            return new EasyAccessMap($headers);
        }
        else
        {
            // If the above function doesn't exist, iterate $_SERVER and collect
            // all HTTP_ variables.
            $headers = [];

            foreach ($_SERVER as $key => $value)
            {
                if ("HTTP_" != substr($key, 0, 5))
                    continue;

                // Not so hellish anymore now that I've stopped caring about
                // the case.
                $newKey = str_replace('_', '-', strtolower(substr($key, 5)));

                $headers += [$newKey => $value];
            }

            return new EasyAccessMap($headers);
        }
    }
    
    /**
     * Get the POST body, attempt to decode it, and then return it.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     */
    public static function getPostBody(string $contentType = ""): mixed
    {
        // $_POST is only used by x-www-form-urlencoded
        // and multipart/form content types. If this
        // is available, return it.
        if (!empty($_POST))
        {
            return $_POST;
        }

        // Otherwise, the input needs to be retrieved and
        // decoded manually.
        $body = @file_get_contents("php://input");

        if (false == $body) return null; // Validate

        // Switch on the content type and attempt to
        // automatically parse contents.
        switch ($contentType)
        {
            case "application/json":
                $obj = @json_decode($body);

                if (null != $obj)
                {
                    return $obj;
                }
                else
                {
                    return $body;
                }

                break;
        }

        // Otherwise return the raw input.
        return $body;
    }
}