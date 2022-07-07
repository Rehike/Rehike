<?php

\Rehike\TemplateFunctions::register('getUrl', function($obj) {
    if (isset($obj->navigationEndpoint->commandMetadata->webCommandMetadata->url)) {
        return $obj->navigationEndpoint->commandMetadata->webCommandMetadata->url;
    } else {
        return '';
    }
});