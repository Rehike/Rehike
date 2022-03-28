<?php

registerFunction('changeThumbSize', function($url, $newSize): string {
    return preg_replace('/=s(.*)-c-k/', '=s' . $newSize . '-c-k', $url);
});