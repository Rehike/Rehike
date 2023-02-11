<?php
\Rehike\TemplateFunctions::register("getThumb", function(?object $container, int $height = 0): ?string {
    if (!isset($container->thumbnails)) return null;
    
    $thumbs = &$container->thumbnails;
    $thumb = null;
    foreach ($thumbs as $ithumb)
    {
        if (isset($ithumb->height) && $ithumb->height >= $height)
        {
            $thumb = $ithumb;
        }
    }

    // Fallback if there's no thumbnail that matches the height
    if (is_null($thumb))
    {
        $thumb = $thumbs[count($thumbs) - 1];
    }

    if (
        isset($thumb->width) &&
        isset($thumb->height) &&
        $thumb->width > 0 &&
        $thumb->height > 0
    )
    // See whether or not we need to change
    // the URL. Shorts thumbnails are given
    // in their original aspect ratio, which
    // causes a heavily stretched thumbnail
    // when displayed on Hitchhiker.
    {
        $ratio = $thumb->width / $thumb->height;

        if ($ratio >= 1.7 && $ratio < 1.8)
        {
            return $thumb->url;
        }
        else
        {
            return preg_replace("/\?sqp=.*/", "", $thumb->url);
        }
    }
    
    return $thumb->url;
});