<?php
\Rehike\TemplateFunctions::register("cssPath", function($name, $pref, $constants) {
    $css2x = false;
    if (isset($pref -> f4) && substr($pref -> f4, 0, 1) == "4") 
        $css2x = true;

    if ($css2x && isset($constants -> css2x -> {$name}))
        return $constants -> css2x -> {$name};
    
    return $constants -> css -> {$name} ?? "";
});