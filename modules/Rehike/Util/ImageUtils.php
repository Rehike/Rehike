<?php
namespace Rehike\Util;

/**
 * Utilities for yt3.ggpht.com and i.ytimg.com
 * image URLs
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 */
class ImageUtils
{
    /**
     * Change the size attribute of a yt3.ggpht.com image URL
     */
    public static function changeSize(string $url, int $size): string
    {
        return preg_replace("/=s\d+-/", "=s" . (string) $size . "-", $url);
    }

    /**
     * Change the width attribute of a yt3.ggpht.com image URL
     */
    public static function changeWidth(string $url, int $width): string
    {
        return preg_replace("/-w\d+-/", "-w" . (string) $width . "-", $url);
    }

    /**
     * Change the height attribute of a yt3.ggpht.com image URL
     */
    public static function changeHeight(string $url, int $height): string
    {
        return preg_replace("/-h\d+-/", "-h" . (string) $height . "-", $url);
    }

    /**
     * Change the width and height of a yt3.ggpht.com image URL
     */
    public static function changeDimensions(string $url, int $width, int $height): string
    {
        return self::changeHeight(self::changeWidth($url, $width), $height);
    }
}