<?php
namespace Rehike\Util;

use Rehike\i18n\i18n;

/**
 * General string handler/substring extraction utilities for Rehike.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class ExtractUtils
{
    /**
     * Isolate any count with a set input by replacing other parts of a
     * substring with an empty string.
     * 
     * @param $input       Base string.
     * @param $substrRegex Substrings to replace
     */
    public static function isolateCount(?string $input, string $substrRegex): string
    {
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
    public static function isolateLikeCnt(?string $likeCount): string
    {
        $i18n = i18n::getNamespace("regex");

        $a = self::isolateCount($likeCount, $i18n->get("likeCountIsolator"));

        if ($a != $i18n->get("likeTextDisabled"))
        {
            return $a;
        }
        else
        {
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
    public static function isolateSubCnt(?string $subCount): string
    {
        $i18n = i18n::getNamespace("regex");

        $a = self::isolateCount($subCount, $i18n->get("subscriberCountIsolator"));

        if ($a != $i18n->get("countZero"))
        {
            return $a;
        }
        else
        {
            return '0';
        }
    }
    
    /**
     * Isolate a videos view count from the common view count text.
     * 
     * "120,000 views" => "120,000"
     * "No views" => "0"
     * 
     * @param $viewCount Base string to modify.
     */
    public static function isolateViewCnt(?string $viewCount): string
    {
        $i18n = i18n::getNamespace("regex");
        
        $a = self::isolateCount($viewCount, $i18n->get("viewCountIsolator"));

        if ($a != $i18n->get("countZero"))
        {
            return $a;
        }
        else
        {
            return '0';
        }
    }
    
    /**
     * Resolve the date string to use within a video's secondary info renderer
     * above the description on the watch page.
     * 
     * @param string|object $date       Original date.
     * @param bool          $isPrivate  Is the video unlisted or private?
     * 
     * @return string
     */
    public static function resolveDate(string|object $date, bool $isPrivate = false): string
    {
        $i18n = i18n::getNamespace("regex");
        $misc = i18n::getNamespace("misc");

        if (is_object($date)) $date = $date->simpleText;
        if (!preg_match($i18n->get("nonPublishCheck"), $date))
        {
            $string = $isPrivate ? "dateTextPrivate" : "dateTextPublic";
            return $misc->format($string, $date);
        }
        else
        {
            return $date;
        }
    }
}