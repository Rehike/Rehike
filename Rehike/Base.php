<?php

namespace Rehike;

use ArgumentCountError;

abstract class Base {
    public static function __callStatic($name, $args) {
        // one clusterfuck of a function (for a static getter/setter)

        $isGet = false;
        switch (substr($name, 0, 3)) {
            case 'get':
                $isGet = true;
            case 'set':
                // search for name (uppercase or lowercase) and return pointer
                $searchForName = function &($name, $tryLowercase = false) use (&$searchForName) {
                    try {
                        return static::$$name;
                    } catch (\Throwable $e) {
                        if ($tryLowercase) {
                            throw new \Exception('Property is not static or does not exist.');
                        }

                        if (!$tryLowercase) {
                            return $searchForName(lcfirst($name), true);
                        }
                    }
                };

                /*
                 * substr - first 3 chars of input name; to be tested against probable
                 * lowercase variant
                 */
                $property = &$searchForName(substr($name, 3, strlen($name) - 3));

                if ($isGet) {
                    return $property;
                } else {
                    // premature return if no arguments
                    if (!isset($args[0]) || count($args) != 1) {
                        throw new ArgumentCountError();
                    }

                    $property = $args[0];
                }

                break;
        }
    }
}