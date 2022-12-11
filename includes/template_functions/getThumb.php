<?php

\Rehike\TemplateFunctions::register('getThumb', function($obj, $height = 0) {
    if (isset($obj -> thumbnail -> thumbnails)) {
        $thumbs = $obj -> thumbnail -> thumbnails;
    } else if (isset($obj -> thumbnails)) {
        $thumbs = $obj -> thumbnails;
    }

    if (!isset($thumbs)) return "//i.ytimg.com/";

    if (isset($height) && $height != 0){
        for ($i = 0; $i < count($thumbs); $i++) {
            if ($thumbs[$i] -> height >= $height) {
                $thumb = $thumbs[$i];
            }
        }
    } else {
        $thumb = $thumbs[array_key_last($thumbs)];
    }

    if (isset($thumb -> width) && isset($thumb -> height)) {
        $ratio = $thumb -> width / $thumb -> height;

        if ($ratio >= 1.7 && $ratio < 1.8) {
            return $thumb -> url;
        } else {
            return preg_replace("/\?sqp=.*/", "", $thumb -> url);
        }
    } else {
        return $thumb -> url;
    }
});