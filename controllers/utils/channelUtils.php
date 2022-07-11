<?php

use \Rehike\Request;

class ChannelUtils {
    public static function getUcid($routerUrl): string {
        switch($routerUrl->path[0]) {
            case 'channel':
                return $routerUrl->path[1] ?? '';
                break;
            case 'user':
            case 'c':
                $response = Request::innertubeRequest("navigation/resolve_url", (object) [
                    "url" => "https://www.youtube.com/" . $routerUrl->path[0] . "/" . $routerUrl->path[1]
                ]);
                $ytdata = json_decode($response);
                return $ytdata->endpoint->browseEndpoint->browseId;
                break;
        }
    }

    public static function synthesiseChannelAvatarSize100Url($url): string {
        return str_replace('s48', 's100', $url);
    }
}