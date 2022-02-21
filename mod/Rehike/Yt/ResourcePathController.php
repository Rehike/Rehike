<?php
namespace Rehike\Yt;

class ResourcePathController
{
    public static $constants;

    public static function pushConstants($c)
    {
        self::$constants = $c;
    }
}