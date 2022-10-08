<?php
namespace Rehike\Util;

use Rehike\i18n;

class ExtractUtils {
    public static function isolateCount(?string $input, string $substrRegex): string {
        if (!$input) return '';
        return preg_replace(
            $substrRegex,
            '',
            $input
        );
    }
    
    public static function isolateLikeCnt(?string $likeCount): string {
        $i18n = i18n::getNamespace("main/regex");

        $a = self::isolateCount($likeCount, $i18n -> get("likeCountIsolator"));
        if ($a != $i18n -> get("likeTextDisabled")) {
            return $a;
        } else {
            return '';
        }
    }
    
    public static function isolateSubCnt(?string $subCount): string {
        $i18n = i18n::getNamespace("main/regex");

        $a = self::isolateCount($subCount, $i18n -> get("subscriberCountIsolator"));
        if ($a != $i18n -> get("subscriberCountZero")) {
            return $a;
        } else {
            return '0';
        }
    }
    
    public static function isolateViewCnt(?string $viewCount): string {
        $i18n = i18n::getNamespace("main/regex");
        
        $a = self::isolateCount($viewCount, $i18n -> get("viewCountIsolator"));
        if ($a != 'No') {
            return $a;
        } else {
            return '0';
        }
    }
    
    public static function resolveDate($date, $isPrivate = false) {
        $i18n = i18n::getNamespace("main/regex");
        $misc = i18n::getNamespace("main/misc");

        if (is_object($date)) $date = $date->simpleText;
        if (!preg_match($i18n -> get("nonPublishCheck"), $date)) {
            $string = $isPrivate ? "dateTextPrivate" : "dateTextPublic";
            return $misc -> get($string, $date);
        } else {
            return $date;
        }
    }
}