<?php
\Rehike\TemplateFunctions::register("imgPath", function($name, $constants) {
    return $constants -> img -> {$name} ?? "";
});