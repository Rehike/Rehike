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
    public static function __callStatic($name, $args)
    {
        if ("get" == substr($name, 0, 3))
        {
            return self::_getBehaviour(
                self::_transformName("get", $name)
            );
        }
        else if ("set" == substr($name, 0, 3))
        {
            return self::_setBehaviour(
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
     * @return void
     */
    protected static function configFromArray($arr)
    {
        $reflection = new ReflectionClass(get_called_class());

        // Get a list of the static properties so that I can
        // compare them.
        $props = $reflection->getStaticProperties();

        foreach ($arr as $key => $value)
        {
            if (!isset($props[$key])) continue;

            self::_setBehaviour($key, $value);
        }
    }

    /**
     * Transform property names for individual function
     * calls (getters/setters).
     * 
     * @param string $prefix
     * @param string $name
     * @return string
     */
    private static function _transformName($prefix, $name)
    {
        $name = str_replace($prefix, "", $name, 1);
        $name = lcfirst($name);

        return $name;
    }

    /**
     * Implements the standard "getter" behaviour.
     * 
     * @param ReflectionClass $reflection
     * @param string $name
     * 
     * @return mixed
     */
    private static function _getBehaviour($name)
    {
        return static::$$name;
    }

    /**
     * Implements the standard "setter" behaviour.
     * 
     * @param ReflectionClass $reflection
     * @param string $name
     * @param mixed $value
     * 
     * @return void
     */
    private static function _setBehaviour($name, $value)
    {
        static::$$name = $value;
    }
}