<?php

namespace CacheUtils;
require_once('encodeStdClass.php');

class Main {
    const DEFAULT_CACHE_PATH = 'cache';

    // TODO: this doesn't work so well
    public static function validateStdClass(string $code): bool {
        // Just in case a cachefile ever gets corrupted, we want to
        // throw that out before the PHP processor kills itself
        // when attempting to work with it.
        try {
            eval('(function(){' . $code . '})();');
        } catch (\Throwable $t) {
            return false;
        }
        return true;
    }

    public static function encodeStdClass(object $object): string {
        return StdClass::encode($object);
    }

    public static function resolvePath(string $path): string {
        // By default, paths resolve to a child of the default
        // caching path. If the end-user wishes to override this
        // behaviour, all they need to do is prefix the string with
        // a slash to include from (project) root.

        if ($path[0] != '/') {
            $path = self::DEFAULT_CACHE_PATH . $path;
        }

        return $path;
    }
    
    public static function cache(string $path, $data): void {
        $path = resolvePath($path);

        // todo finish
    }
}