<?php
\Rehike\TemplateFunctions::register("jsPath", function($name, $constants) {
    return $constants -> js -> {$name} ?? "";
});