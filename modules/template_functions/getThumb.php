<?php

\Rehike\TemplateFunctions::register('getThumb', function($obj, $height = 110, $thumbList = "thumbnail") {
    if (isset($obj -> $thumbList -> thumbnails)) {
        $thumbs = $obj -> $thumbList -> thumbnails;
    } else if (isset($obj -> thumbnails)) {
        $thumbs = $obj -> thumbnails;
    }

    if (!isset($thumbs)) return "//i.ytimg.com/invalid_thumb";

    for ($i = 0; $i < count($thumbs); $i++) {
        if ($thumbs[$i] -> height >= $height) {
            return $thumbs[$i] -> url;
        }
    }

    // fallback if it does not find any thumbnail bigger or equal to size specified
    return $thumbs[count($thumbs)];
});