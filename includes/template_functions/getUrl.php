<?php

\Rehike\TemplateFunctions::register('getUrl', function($obj) {
    return @$obj->navigationEndpoint->commandMetadata->webCommandMetadata->url
        ?? @$obj->navigationEndpoint->confirmDialogEndpoint->content->confirmDialogRenderer->confirmButton->buttonRenderer->command->urlEndpoint->url
        ?? @$obj -> commandMetadata -> webCommandMetadata -> url
        ?? "";
});