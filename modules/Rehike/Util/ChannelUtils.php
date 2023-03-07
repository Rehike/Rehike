<?php
namespace Rehike\Util;

use \Rehike\Request;

/**
 * General utilties for channels.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class ChannelUtils {
    /**
     * Get a channel's UCID from an internal request URL.
     */
    public static function getUcid($request): ?string {
        if (in_array($request->path[0], ["channel", "user", "c"])) {
            switch($request->path[0]) {
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
                    return self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
                    break;
            }
        }

        return self::getUcidFromUrl(explode("?", $_SERVER["REQUEST_URI"])[0]);
    }

    /**
     * Get a channel's UCID from a URL.
     */
    private static function getUcidFromUrl(string $url): ?string
    {
        Request::queueInnertubeRequest("resolve", "navigation/resolve_url", (object) [
            "url" => "https://www.youtube.com" . $url
        ]);
        $response = json_decode(Request::getResponses()["resolve"]);

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
            Request::queueInnertubeRequest("resolve2", "navigation/resolve_url", (object) [
                "url" => $response->endpoint->urlEndpoint->url
            ]);
            $response2 = json_decode(Request::getResponses()["resolve2"]);

            return $response2->endpoint->browseEndpoint->browseId ?? null;
        }

        return null;
    }
}