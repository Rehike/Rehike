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
    /**
     * Encode a string into URL-formatted base64.
     * 
     * @param string $data
     * @return string
     */
    public static function encode($data)
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($data)
        );
    }

    /**
     * Decode a string from URL-formatted base64.
     * 
     * @param string $data
     * @return string
     */
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