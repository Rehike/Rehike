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
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    }

    public static function decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
    }
}