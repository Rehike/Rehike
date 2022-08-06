<?php

\Rehike\TemplateFunctions::register("getDescSnippet", function ($renderer) {
    if (isset($renderer -> descriptionSnippet)) return $renderer -> descriptionSnippet;
    if (isset($renderer -> detailedMetadataSnippets[0] -> snippetText)) return $renderer -> detailedMetadataSnippets[0] -> snippetText;
});