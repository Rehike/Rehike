<?php

/**
 * An fnmatch polyfill for certain PHP configurations, most
 * notably old PHP on Windows.
 * 
 * @see https://www.php.net/manual/en/function.fnmatch.php#100207
 */
if (!function_exists("fnmatch"))
{
    define("FNM_PATHNAME", 1);
    define("FNM_NOESCAPE", 2);
    define("FNM_PERIOD", 4);
    define("FNM_CASEFOLD", 16);

    /**
     * @param string $pattern
     * @param string $filename
     * @param int $flags
     */
    function fnmatch($pattern, $filename, $flags)
    {
        $regexFlags = "";

        // Declare all possible transformations from
        // glob syntax to regexp syntax
        $transforms = [
            "\*" => ".*",
            "\?" => ".",
            "\[\!" => "[^",
            "\[" => "[",
            "\]" => "]",
            "\." => "\.",
            "\\" => "\\\\"
        ];

        // Handle flags behaviours
        if ($flags & FNM_PATHNAME) $transforms["\*"] = "[^/]*";
        if ($flags & FNM_NOESCAPE) unset($transforms["\\"]);
        if ($flags & FNM_PERIOD && 0 === strpos($filename, ".") && 0 !== strpos($pattern, "."))
            return false;
        if ($flags & FNM_CASEFOLD) $regexFlags .= "i";

        // Declare regexp pattern
        $regexPattern = "#^"
            . strtr( preg_quote($pattern, "#"), $transforms )
            . "$#"
            . $regexFlags
        ;

        return (bool)preg_match($regexPattern, $filename);
    }
}