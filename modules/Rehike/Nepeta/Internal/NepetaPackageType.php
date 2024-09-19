<?php
namespace Rehike\Nepeta\Internal;

/**
 * The type of a Nepeta package.
 * 
 * @enum
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaPackageType
{
    public const TYPE_EXTENSION = 0;
    public const TYPE_THEME = 1;

    public static function fromString(string $str): int
    {
        return match($str) {
            "extension" => self::TYPE_EXTENSION,
            "theme" => self::TYPE_THEME,

            default => self::TYPE_EXTENSION
        };
    }
}