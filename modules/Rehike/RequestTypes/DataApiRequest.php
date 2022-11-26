<?php
namespace Rehike\RequestTypes;

use Rehike\Request;

/**
 * Implements the behaviour needed to request the public YouTube Data API v3,
 * without any limitations.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
trait DataApiRequest
{
    /**
     * Add a Data API request to the queue.
     * 
     * @param string $id      for the request queue
     * @param string $action  to use (part after v3 in the URL)
     * @param object $params  for either url or body, depends on $post
     * @param bool   $post    use POST or not?
     */
    public static function queueDataApiRequest($id, $action, $params, $post = false)
    {
        $urlParams = "";

        if (!$post) {
            foreach($params as $name => $value) {
                $urlParams .= "&{$name}={$value}";
            }
        }

        $headers = [
            "X-Origin" => "https://explorer.apis.google.com/",
            "Accept" => "application/json"
        ];

        if ($post) {
            $headers += [
                "Content-Type" => "application/json"
            ];
        }

        $body = [
            "headers" => $headers
        ];

        if ($post) {
            $body += [
                "body" => $params,
                "post" => true
            ];
        } else {
            $body += [
                "post" => false
            ];
        }

        Request::queueRequest(
            "https://www.googleapis.com/youtube/v3/{$action}?key=AIzaSyAa8yy0GdcGPHdtD083HiGGx_S0vMPScDM{$urlParams}",
            $body,
            Request::NS_DATAAPI,
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
    public static function dataApiRequest($action, $body = null, $cname = "WEB", $cver = "2.20220303.01.01")
    {
        return Request::singleRequestWrapper(function() use ($action, $body, $cname, $cver) {
            self::queueInnertubeRequest("singleRequest", $action, $body, $cname, $cver);
        });
    }
}