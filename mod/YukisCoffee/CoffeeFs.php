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

    }
}