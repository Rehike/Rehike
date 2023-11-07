<?php
namespace Rehike;

/**
 * Structured Page Fragments JS library utilities for Rehike.
 * 
 * This is used for providing support for YouTube's SPF.js library for
 * single-page applications.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Spf
{
    /**
     * Look at the request parameters and determine if SPF
     * was requested (over the normal state).
     * 
     * @return string|null if string, the SPF state (navigate/etc.)
     */
    public static function isSpfRequested()
    {
        if (isset($_GET["spf"]))
        {
            switch ($_GET["spf"])
            {
                case "navigate":
                case "navigate-back":
                case "navigate-forward":
                case "load":
                    return $_GET["spf"];
            }
        }

        return false;
    }
}