<?php
namespace Rehike\RequestTypes;

use Rehike\Request;

/**
 * Implements a basic URL request type.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
trait UrlRequest
{
    /**
     * Add a URL request to the queue.
     * 
     * @param string $id for the request queue
     * @param string $url to request
     * @param string $namespace to optionally use
     */
    public static function queueUrlRequest($id, $url, $options = [], $namespace = Request::NS_URL)
    {
        $namespacedId = "{$namespace}_{$id}";

        $host = "https://www.youtube.com";

        // Convert relative links
        if (0 == strpos($url, "/")) $url = $host . $url;

        Request::queueRequest($url, $options, $namespace, $id);
    }

    /**
     * Perform a single URL request.
     */
    public static function urlRequest($url, $options = [], $namespace = Request::NS_URL)
    {
        return self::singleRequestWrapper(function() use ($url, $options, $namespace) {
            self::queueUrlRequest("singleRequest", $url, $options, $namespace);
        });
    }
}