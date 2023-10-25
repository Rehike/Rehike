<?php
namespace Rehike\Util;

/**
 * A set of utilities for parsing YouTube's PREF cookie.
 * 
 * @author Aubrey Pankow <aubymori@gmail.com>
 * @author The Rehike Maintainers
 */
class PrefUtils
{
    /**
     * Decode the PREF cookie into an object.
     */
    public static function decode(string $pref): object
    {
        $response = (object) [];
        $temp = explode("&", $pref);

        foreach ($temp as $value)
        {
            $temp2 = explode("=", $value);
            $response->{$temp2[0]} = $temp2[1] ?? "";
        }

        return $response;
    }

    /**
     * Encode the decoded PREF object back into a cookie string.
     */
    public static function encode(object $pref): string
    {
        $temp = [];
        foreach ($pref as $key => $value)
        {
            $temp[] = "$key=$value";
        }
        return implode("&", $temp);
    }

    /**
     * Is autoplay enabled?
     * 
     * @var object $pref  Parsed PREF value.
     */
    public static function autoplayEnabled(object $pref): bool
    {
        if (isset($pref->f5) && substr($pref->f5, 0, 1) == "3")
        {
            return false;
        }

        return true;
    }
}