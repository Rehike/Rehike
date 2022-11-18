<?php
namespace Rehike\Util;

/**
 * Trait for generating URL format base64.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Base64Url
{
    public static function encode($data)
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($data)
        );
    }

    public static function decode($data)
    {
        return base64_decode(
            str_replace(
                ["-", "_", "%3D"],
                ["+", "/", ""],
                $data
            )
        );
    }
}