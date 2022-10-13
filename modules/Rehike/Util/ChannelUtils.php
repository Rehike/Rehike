<?php
namespace Rehike\Util;

use \Rehike\Request;

class ChannelUtils {
    public static function getUcid($request): string {
        if (in_array($request -> path[0], ["channel", "user", "c"])) {
            switch($request -> path[0]) {
                case "channel":
                    return $request -> path[1] ?? "";
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