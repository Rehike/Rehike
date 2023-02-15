<?php
namespace Rehike\Util;

use Rehike\i18n;

/**
 * General string handler/substring extraction utilities for Rehike.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class ExtractUtils {
    /**
     * Isolate any count with a set input by replacing other parts of a
     * substring with an empty string.
     * 
     * @param $input       Base string.
     * @param $substrRegex Substrings to replace
     */
    public static function isolateCount(?string $input, string $substrRegex): string {
        if (!$input) return '';
        return preg_replace(
            $substrRegex,
            '',
            $input
        );
    }
    
    /**
     * Isolate a watch page's like count from the accessibility string.
     * 
     * @param $likeCount Base string to modify.
     */
    public static function isolateLikeCnt(?string $likeCount): string {
        $i18n = i18n::getNamespace("main/regex");

        $a = self::isolateCount($likeCount, $i18n->get("likeCountIsolator"));
        if ($a != $i18n->get("likeTextDisabled")) {
            return $a;
        } else {
            return '';
        }
    }
    
    /**
     * Isolate a channel's subscriber count from the subscriber count text.
     * 
     * Ordinarily, counts are returned in a format like "1.23K subscribers".
     * 
     * @param $subCount Base string to modify.
     */
    public static function isolateSubCnt(?string $subCount): string {
        $i18n = i18n::getNamespace("main/regex");

        $a = self::isolateCount($subCount, $i18n->get("subscriberCountIsolator"));
        if ($a != $i18n->get("countZero")) {
            return $a;
        } else {
            return '0';
        }
    }
    
    /**
     * Isolate a videos view count from the common view count text.
     * 
     * "120,000 views" => "120,000"
     * "No views" => "0"
     * 
     * BUG(dcooper): This function will only work in English, as although the
     * isolator string is obtained from i18n, the "No" substring is still
     * hardcoded in English only.
     * 
     * @param $viewCount Base string to modify.
     */
    public static function isolateViewCnt(?string $viewCount): string {
        $i18n = i18n::getNamespace("main/regex");
        
        $a = self::isolateCount($viewCount, $i18n->get("viewCountIsolator"));
        if ($a != $i18n->get("countZero")) {
            return $a;
        } else {
            return '0';
        }
    }
    
    /**
     * Resolve the date string to use within a video's secondary info renderer
     * above the description on the watch page.
     * 
     * @param string $date      Original date.
     * @param string $isPrivate Is the video not publicly available?
     * 
     * @return string
     */
    public static function resolveDate($date, $isPrivate = false) {
        $i18n = i18n::getNamespace("main/regex");
        $misc = i18n::getNamespace("main/misc");

        if (is_object($date)) $date = $date->simpleText;
        if (!preg_match($i18n->get("nonPublishCheck"), $date)) {
            $string = $isPrivate ? "dateTextPrivate" : "dateTextPublic";
            return $misc->get($string, $date);
        } else {
            return $date;
        }
    }
}