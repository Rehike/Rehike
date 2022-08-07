<?php
namespace Rehike\Util;

/**
 * Utilities for yt3.ggpht.com and i.ytimg.com
 * image URLs
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 */
class ImageUtils {
    /**
     * Change the size attribute of a yt3.ggpht.com image URL
     * 
     * @param $url
     */
    public static function changeGgphtImageSize(string $url, int $newSize) {
        return preg_replace("/=s\d+-/", "=s" . (string) $newSize . "-", $url);
    }
}