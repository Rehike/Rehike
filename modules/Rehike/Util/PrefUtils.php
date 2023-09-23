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
     * Parse the PREF cookie.
     */
    public static function parse(string $pref): object
    {
        $response = (object) [];
        $temp = explode("&", $pref);

        foreach ($temp as $value)
        {
            $temp2 = explode("=", $value);
            $response->{$temp2[0]} = $temp2[1];
        }

        return $response;
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