<?php
namespace Rehike\RequestTypes;

use Rehike\Request;

/**
 * Implements the request type for extracting initial data from a downloaded
 * YouTube HTML.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
trait InitialDataRequest
{
    public static function queueInitialDataRequest($id, $url)
    {
        return self::queueUrlRequest($id, $url, Request::NS_INITIALDATA);
    }

    public static function initialDataRequest($url)
    {
        return self::singleRequestWrapper(function() use ($url) {
            self::queueInitialDataRequest("singleRequest", $url);
        });
    }
}