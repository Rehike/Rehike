<?php
namespace YukisCoffee;

class CoffeeFs
{
    public static function fileExists($filename)
    {
        return \file_exists($filename);
    }

    public static function folderExists($path)
    {
        return \is_dir($path);
    }

    public static function writeFile($path, $content)
    {
        if (!is_array($path)) $path = self::pathToArray($path);
        $filename = $path[count($path) - 1]; // Store last item
        array_splice($path, count($path) - 1, 1); // Remove last item

        if (!self::folderExists($path))
        {
            self::writeDirRecursive($path);
        }

        $fhandle = fopen($filename, "w");
        fwrite($fhandle, $content);
        fclose($fhandle);

        return true;
    }

    public static function writeDirRecursive($path)
    {
        if (!is_array($path)) $path = implode("/", $path);

        \mkdir($path, 0777, true);
    }

    public static function pathToArray($path)
    {
        // Replace Windows path separator \\ with /
        str_replace("\\\\", "/", $path);
        $path = explode("/", $path);
        
        // Remove potentially empty first item
        if ("" === $path[0]) array_splice($path, 0, 1);
        return $path;
    }
}