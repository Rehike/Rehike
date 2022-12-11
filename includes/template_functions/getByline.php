<?php

\Rehike\TemplateFunctions::register("getByline", function($renderer) {
    if (isset($renderer->longBylineText)) return $renderer->longBylineText;
    if (isset($renderer->shortBylineText)) return $renderer->shortBylineText;
    return null;
});