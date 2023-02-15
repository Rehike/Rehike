<?php

\Rehike\TemplateFunctions::register("getMeta", function ($renderer) {
    $VALID_METAS = [
        "viewCountText",
        "publishedTimeText",
        "videoCountText"
    ];
    $metas = [];

    foreach($VALID_METAS as $meta) {
        if (isset($renderer -> {$meta})
        &&  (
            isset($renderer -> {$meta} -> simpleText) ||
            isset($renderer -> {$meta} -> runs)
        )) {
            $metas[] = $renderer -> {$meta};
        }
    }

    return $metas;
});