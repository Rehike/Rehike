<?php
namespace Rehike\Util;

use \Rehike\Request;

class ChannelUtils {
    public static function getUcid($request): string {
        switch($request -> path[0]) {
            case "channel":
                return $request -> path[1] ?? "";
                break;
            case "user":
            case "c":
                $response = Request::innertubeRequest("navigation/resolve_url", (object) [
                    "url" => "https://www.youtube.com/" . $request -> path[0] . "/" . $request -> path[1]
                ]);
                $ytdata = json_decode($response);
                if (isset($ytdata -> endpoint -> browseEndpoint -> browseId)) {
                    return $ytdata -> endpoint -> browseEndpoint -> browseId;
                } else {
                    return "";
                }
                break;
        }
    }

    public static function synthesiseChannelAvatarSize100Url($url): string {
        return str_replace("s48", "s100", $url);
    }
}