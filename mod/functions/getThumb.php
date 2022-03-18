<?php

registerFunction('getThumb', function($obj, $height = 110) {
    if (isset($obj -> thumbnail)) {
        $thumbs = $obj -> thumbnail -> thumbnails;
    } else if (isset($obj -> thumbnails)) {
        $thumbs = $obj -> thumbnails[0] -> thumbnails;
    } else {
        return;
    }

    for ($i = 0; $i < count($thumbs); $i++) {
        if ($thumbs[$i] -> height >= $height) {
            return $thumbs[$i] -> url;
        }
    }
});