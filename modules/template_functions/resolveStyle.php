<?php
\Rehike\TemplateFunctions::register("resolveStyle", function($const) {
    $styleOverrides = (object) [
        "STYLE_BLUE_TEXT" => "STYLE_PRIMARY"
    ];

    if (isset($styleOverrides -> {$const})) {
        $const = $styleOverrides -> {$const};
    }

    return strtolower(str_replace(["STYLE_", "_"], ["", "-"], $const));
});