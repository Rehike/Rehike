<?php
namespace Rehike\ControllerV2\Util;

/**
 * Convert a glob to a regexp. This is used by Controller v2.
 * 
 * This implementation is somewhat nonstandard, but it works for the
 * purpose Rehike uses it. Probably unsafe to use it elsewhere...
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GlobToRegexp
{
    public const PATHNAME = 1;
    public const NOESCAPE = 2;
    public const PERIOD = 4;
    public const CASEFOLD = 16;

    /**
     * Convert a glob to a regex pattern.
     */
    public static function convert(string $pattern, string $filename, int $flags = 0): string
    {
        $regexFlags = "";

        // Declare all possible transformations from
        // glob syntax to regexp syntax
        $transforms = [
            "/\*\*" => "([\?|\/].*)?",
            "\*" => ".*",
            "\?" => ".",
            "\[\!" => "[^",
            "\[" => "[",
            "\]" => "]",
            "\(" => "(",
            "\)" => ")",
            "\|" => "|",
            "\." => "\.",
            "\\" => "\\\\"
        ];

        // Handle flags behaviours
        if ($flags & self::PATHNAME)
        {
            $transforms["\*"] = "[^/]*";
        }
        
        if ($flags & self::NOESCAPE)
        {
            unset($transforms["\\"]);
        }
        
        if ($flags & self::PERIOD && 0 === strpos($filename, ".") && 0 !== strpos($pattern, "."))
        {
            return false;
        }
        
        if ($flags & self::CASEFOLD)
        {
            $regexFlags .= "i";
        }

        // Declare regexp pattern
        $regexPattern = "#^"
            . strtr( preg_quote($pattern, "#"), $transforms )
            . "$#"
            . $regexFlags
        ;

        return $regexPattern;
    }

    /**
     * Match a glob to a regex pattern.
     * 
     * `match` is a reserved word as of PHP 8, so the method is called
     * doMatch instead.
     */
    public static function doMatch(string $pattern, string $filename, int $flags = 0): bool
    {
        $regexPattern = GlobToRegexp::convert($pattern, $filename, $flags);

        return (bool)preg_match($regexPattern, $filename);
    }
}