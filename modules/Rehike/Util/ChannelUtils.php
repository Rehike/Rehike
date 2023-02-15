<?php
namespace Rehike\Util;

use Rehike\Network;
use YukisCoffee\CoffeeRequest\Promise;

use function Rehike\Async\async;

/**
 * General utilties for channels.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ChannelUtils
{
    /**
     * Get a channel's UCID from an internal request URL.
     * 
     * @return Promise<?string>
     */
    public static function getUcid($request): Promise/*<?string>*/
    {
        return async(function() use (&$request) {
            if (in_array($request->path[0], ["channel", "user", "c"]))
            {
                switch($request->path[0])
                {
                    case "channel":
                        $ucid = $request->path[1] ?? "";
                        if (substr($ucid, 0, 2) == "UC")
                        {
                            return $ucid;
                        }
                        else
                        {
                            return "";
                        }
                        break;
                    case "user":
                    case "c":
                        return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
                        break;
                }
            }

            return yield self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
        });
    }

    /**
     * Get a channel's UCID from a URL.
     * 
     * @return Promise<?string>
     */
    private static function getUcidFromUrl(string $url): Promise/*<?string>*/
    {
        return async(function() use (&$url) {
            $response = (yield Network::innertubeRequest(
                action: "navigation/resolve_url",
                body: [ "url" => "https://www.youtube.com" . $url ]
            ))->getJson();
    
            if (isset($response->endpoint->browseEndpoint->browseId))
            {
                return $response->endpoint->browseEndpoint->browseId;
            }
            // For some handles, resolve_url returns a classic channel
            // URL (e.g. https://www.youtube.com/jawed). For every case
            // that this happens, you can just make another resolve_url
            // request, and it will actually give you the UCID of the
            // channel.
            else if (isset($response->endpoint->urlEndpoint->url))
            {
                $response2 = (yield Network::innertubeRequest(
                    action: "navigation/resolve_url",
                    body: [ "url" => $response->endpoint->urlEndpoint->url ]
                ))->getJson();
    
                return $response2->endpoint->browseEndpoint->browseId ?? null;
            }
    
            return null;
        });
    }
}