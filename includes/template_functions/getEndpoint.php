<?php

\Rehike\TemplateFunctions::register('getEndpoint', function($obj) {
    if (isset($obj->navigationEndpoint->commandMetadata->webCommandMetadata->url)) {
        return $obj->navigationEndpoint->commandMetadata->webCommandMetadata->url;
    } else {
        return '';
    }
});