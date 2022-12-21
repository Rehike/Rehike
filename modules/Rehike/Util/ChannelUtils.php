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
    public static function getUcid($request): string {
        if (in_array($request -> path[0], ["channel", "user", "c"])) {
            switch($request -> path[0]) {
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
                    Request::queueInnertubeRequest("resolve", "navigation/resolve_url", (object) [
                        "url" => "https://www.youtube.com/" . $request -> path[0] . "/" . $request -> path[1]
                    ]);
                    $response = Request::getResponses()["resolve"];

                    $ytdata = json_decode($response);
                    if (isset($ytdata -> endpoint -> browseEndpoint -> browseId)) {
                        return $ytdata -> endpoint -> browseEndpoint -> browseId;
                    } else {
                        return "";
                    }
                    break;
            }
        } else {
            Request::queueInnertubeRequest("resolve", "navigation/resolve_url", (object) [
                "url" => "https://www.youtube.com/" . $request -> path[0]
            ]);
            $response = Request::getResponses()["resolve"];

            $ytdata = json_decode($response);
            if (isset($ytdata -> endpoint -> browseEndpoint -> browseId)) {
                return $ytdata -> endpoint -> browseEndpoint -> browseId;
            } else {
                return "";
            }
        }
    }
}