<?php

registerFunction('resourcePath', function ($consts, $type, $name) {
    return $consts->{$type}->{$name} ?? null;
});