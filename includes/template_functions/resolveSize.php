<?php
\Rehike\TemplateFunctions::register("resolveSize", function($const) {
    return strtolower(str_replace(["SIZE_", "_"], ["", "-"], $const));
});