<?php

\Rehike\TemplateFunctions::register("getMeta", function ($renderer) {
    if (isset($renderer->dateBeforeViews) && $renderer->dateBeforeViews)
    {
        $dateViews = [
            "publishedTimeText",
            "viewCountText"
        ];
    }
    else
    {
        $dateViews = [
            "viewCountText",
            "publishedTimeText"
        ];
    }

    $VALID_METAS = $dateViews + [
        "videoCountText"
    ];

    $metas = [];

    foreach ($VALID_METAS as $meta)
    {
        if (
            isset($renderer->{$meta}) && (
                isset($renderer->{$meta} ->simpleText) ||
                isset($renderer->{$meta} ->runs)
            )
        )
        {
            $metas[] = $renderer->{$meta};
        }
    }

    return $metas;
});