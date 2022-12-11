<?php
use \Rehike\ControllerV2\Util\GlobToRegexp;

/**
 * An fnmatch polyfill for certain PHP configurations, most
 * notably old PHP on Windows.
 * 
 * This is based on the GlobToRegexp behaviour of ControllerV2. With
 * that said, I got lazy writing this. If you're moving CV2 at all,
 * make sure to accomodate those changes here.
 * Love, Taniko.
 * 
 * @see https://www.php.net/manual/en/function.fnmatch.php#100207
 */
if (!function_exists("fnmatch"))
{
    define("FNM_PATHNAME", GlobToRegexp::PATHNAME);
    define("FNM_NOESCAPE", GlobToRegexp::NOESCAPE);
    define("FNM_PERIOD", GlobToRegexp::PERIOD);
    define("FNM_CASEFOLD", GlobToRegexp::CASEFOLD);

    /**
     * @param string $pattern
     * @param string $filename
     * @param int $flags
     */
    function fnmatch($pattern, $filename, $flags = 0)
    {
        return GlobToRegexp::doMatch($pattern, $filename, $flags);
    }
}