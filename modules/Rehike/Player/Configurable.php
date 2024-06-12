<?php
namespace Rehike\Player;

use ReflectionClass;
use BadMethodCallException;

/**
 * Implements base configuration behaviours for PlayerCore.
 * 
 * This essentially implements a set of "set" functions for
 * configuring the static properties of a class.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class Configurable
{
    public static function __callStatic(string $name, mixed $args): mixed
    {
        if ("get" == substr($name, 0, 3))
        {
            return self::_getBehavior(
                self::_transformName("get", $name)
            );
        }
        else if ("set" == substr($name, 0, 3))
        {
            return self::_setBehavior(
                self::_transformName("set", $name),
                $args[0]
            );
        }
        else
        {
            throw new BadMethodCallException(
                "Method $name does not exist on " . get_called_class()
            );
        }
    }

    /**
     * Set a list of properties from an array.
     * 
     * @param string[] $arr
     */
    protected static function configFromArray(array $arr): void
    {
        $reflection = new ReflectionClass(get_called_class());

        // Get a list of the static properties so that I can
        // compare them.
        $props = $reflection->getStaticProperties();

        foreach ($arr as $key => $value)
        {
            if (!isset($props[$key])) continue;

            self::_setBehavior($key, $value);
        }
    }

    /**
     * Transform property names for individual function
     * calls (getters/setters).
     */
    private static function _transformName(string $prefix, string $name): string
    {
        // PHP requires this to be passed by reference.
        $value = 1;
        
        $name = str_replace($prefix, "", $name, $value);
        $name = lcfirst($name);

        return $name;
    }

    /**
     * Implements the standard "getter" behavior.
     */
    private static function _getBehavior(string $name): mixed
    {
        return static::$$name;
    }

    /**
     * Implements the standard "setter" behavior.
     */
    private static function _setBehavior(string $name, mixed $value): void
    {
        static::$$name = $value;
    }
}