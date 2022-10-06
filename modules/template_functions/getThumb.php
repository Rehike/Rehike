<?php

\Rehike\TemplateFunctions::register('getThumb', function($obj, $height = 110, $thumbList = "thumbnail") {
    if (isset($obj -> $thumbList -> thumbnails)) {
        $thumbs = $obj -> $thumbList -> thumbnails;
    } else if (isset($obj -> thumbnails)) {
        $thumbs = $obj -> thumbnails;
    }

    if (!isset($thumbs)) return "//i.ytimg.com/";

    for ($i = 0; $i < count($thumbs); $i++) {
        if ($thumbs[$i] -> height >= $height) {
            $thumb =  $thumbs[$i];
        }
    }

    $response = (object) [];
    $response -> url = $thumb -> url;
    $response -> width = ceil(($thumb -> width / $thumb -> height) * $height);
    $response -> height = $height;

    return $response;
});