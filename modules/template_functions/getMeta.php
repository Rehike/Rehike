<?php

\Rehike\TemplateFunctions::register("getMeta", function ($renderer) {
    $metas = [];

    if (isset($renderer->viewCountText)) $metas[] = $renderer->viewCountText;
    if (isset($renderer->publishedTimeText)) $metas[] = $renderer->publishedTimeText;
    if (isset($renderer->videoCountText)) $metas[] = $renderer->videoCountText;

    return $metas;
});