<?php
namespace Rehike;

class SharedFunctions
{
    // Clean up (a little bit) the PHP/Twig shared
    // functions mechanism.
    // In PHP, they shall be called statically as a
    // method of this class.

    public static $functions = [];

    public static function addFunction($name, $callback)
    {
        self::$functions += [$name => $callback];
    }

    public static function __callStatic($name, $arguments)
    {
        if (isset(self::$functions[$name]))
        {
            return self::$functions[$name](...$arguments);
        }
        else
        {
            throw new Exception\RehikeException("Function {$name} does not exist.");
        }
    }
}