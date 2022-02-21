<?php

RehikeRegisterSharedFunction('resourcePath', function ($type, $name) {
    return \Rehike\Yt\ResourcePathController::$constants->{$type}->{$name};
});