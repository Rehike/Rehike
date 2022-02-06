<?php

class ChannelUtils {
    public static function getUcid($routerUrl): string {
        switch($routerUrl->path[0]) {
            case 'channel':
                return $routerUrl->path[1] ?? '';
                break;
            case 'user':
            case 'c':
                $url = $routerUrl->path[0] . '/' . $routerUrl->path[1];
            default:
                // TODO: rewrite this to extract ytInitialData
                $url = $url ?? $routerUrl->path[0];
                $ch = curl_init('https://www.youtube.com/' . $url);
                curl_setopt_array($ch, [
                    CURLOPT_HEADER => 1,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_POST => false,
                    CURLOPT_RETURNTRANSFER => 1
                ]);
                $html = curl_exec($ch);
                curl_close($ch);

                preg_match("'<meta itemprop=\"channelId\" content=\"(.*?)\"'si", $html, $match);
                if($match && $match[1]) {
                    return $match[1];
                }

                break;

                // no result, so 404
        }
    }

    public static function synthesiseChannelAvatarSize100Url($url): string {
        // aubrey crying forever
        return str_replace('s48', 's100', $url);
    }

    const SCT_PROTOBUF_TAB_FIELD = 18; // 0x12 from 2 << 3 | 2
    public static function synthChannelTab(string $tab = 'featured'): string {
        // protobuf, but we can quickly, lazily synthesise it
        return base64_encode(
            chr(self::SCT_PROTOBUF_TAB_FIELD) .
            chr(strlen($tab)) . // should never exceed 127 chars so this is alright
            $tab
        );
    }
}