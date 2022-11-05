<?php
\Rehike\TemplateFunctions::register("cssPath", function($name, $pref, $constants) {
    if (isset($pref -> f4) && substr($pref -> f4, 0, 1) == "4") 
        $prefix = "css2x";
    else
        $prefix = "css";

    return $constants -> {$prefix} -> {$name} ?? "";
});