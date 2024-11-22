<?php
namespace Rehike\Util;

/**
 * Provides common string case conversion utilities.
 * 
 * @author The Rehike Maintainers
 */
class CasingUtils
{
    /**
     * Convert a CONSTANT_CASE string to a hyphen-case string.
     * 
     * This may be useful in a number of areas, but it's particularly
     * useful for HTML IDs, i.e in the guide.
     */
    public static function constantToHyphen(string $constCase): string
    {
        return strtolower(
            str_replace("_", "-", $constCase)
        );
    }
}