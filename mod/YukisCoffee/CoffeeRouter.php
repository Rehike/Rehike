<?php
namespace YukisCoffee;

class CoffeeRouter
{
    // Might be useful sometimes to direct
    // a URL to a file.
    // Maybe even forward path variables or arguments 😳
    public static $filesIncludePath = "";
    public static $pathsRegistry = [];

    public static function setIncludePath($path)
    {
        self::$filesIncludePath = $path;
    }

    public static function registerDefinitions($defs)
    {
        self::$pathsRegistry = $defs;
    }

    public static function route($url)
    {
        
    }
}