<?php
namespace Rehike\Util;

use function base64_encode;
use function base64_decode;

/**
 * Provides common base64 utilities.
 * 
 * @author The Rehike Maintainers
 */
class Base64
{
    public static function encode(string $data): string
    {
        return base64_encode($data);
    }

    public static function decode(string $data): string
    {
        return base64_decode($data);
    }

    public static function urlEncode(string $data): string
    {
        return Base64Url::encode($data);
    }

    public static function urlDecode(string $data): string
    {
        return Base64Url::decode($data);
    }
}