<?php

\Rehike\TemplateFunctions::register("getLockupInfo", function ($renderer) {
    $response = (object) [];

    // Get the name of the renderer
    foreach($renderer as $key => $val) $rendName = $key;
    $response -> info = $renderer -> $rendName;
    $response -> style = (strpos($rendName, "grid") > -1) ? "grid" : "tile";
    $response -> type = strtolower(str_replace("compact", "", str_replace("grid", "", str_replace("Renderer", "", $rendName))));

    if ($a = @$response -> info -> thumbnails[0]) {
        $response -> thumbArray = $a;
    } else if ($a = @$response -> info -> thumbnailRenderer -> showCustomThumbnailRenderer -> thumbnail) {
        $response -> thumbArray = $a;
    } else {
        $response -> thumbArray = $response -> info -> thumbnail ?? null;
    }

    $validTypes = ["video", "channel", "playlist", "radio", "movie", "show"];

    for ($i = 0; $i < count($validTypes); $i++) {
        if ($response -> type == $validTypes[$i]) {
            return $response;
        }
    }
    return null;
});