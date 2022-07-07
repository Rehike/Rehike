<?php

\Rehike\TemplateFunctions::register('resourcePath', function ($consts, $type, $name) {
    return $consts->{$type}->{$name} ?? null;
});