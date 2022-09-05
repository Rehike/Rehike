<?php

\Rehike\TemplateFunctions::register('getUrl', function($obj) {
    if (isset($obj->navigationEndpoint->commandMetadata->webCommandMetadata->url)) {
        return $obj->navigationEndpoint->commandMetadata->webCommandMetadata->url;
    } elseif (isset($obj->navigationEndpoint->confirmDialogEndpoint->content->confirmDialogRenderer->confirmButton->buttonRenderer->command->urlEndpoint->url)) {
        return $obj->navigationEndpoint->confirmDialogEndpoint->content->confirmDialogRenderer->confirmButton->buttonRenderer->command->urlEndpoint->url;
    } else {
        return '';
    }
});