<?php
namespace Rehike\Util;

class PrefUtils {
    /**
     * Parse the PREF cookie.
     * 
     * @var string $pref  PREF cookie.
     * @return object
     */
    public static function parse($pref) {
        $response = (object) [];
        $temp = explode("&", $pref);
        foreach ($temp as $value) {
            $temp2 = explode("=", $value);
            $response -> {$temp2[0]} = $temp2[1];
        }

        return $response;
    }

    /**
     * Is autoplay enabled?
     * 
     * @var object $pref  Parsed PREF value.
     * @return bool
     */
    public static function autoplayEnabled($pref) {
        if (isset($pref -> f5) && substr($pref -> f5, 0, 1) == "3") {
            return false;
        }
        return true;
    }
}