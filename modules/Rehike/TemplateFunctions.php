<?php
namespace Rehike;

/**
 * cleaned up template functions code
 */
class TemplateFunctions
{
    protected static $registry = [];

    // temporary?
    public static $boundTwigInstance;

    public static function __callStatic($name, $args)
    {
        if ($function = @self::$registry[$name])
        {
            return $function(...$args);
        }
        else
        {
            throw new \BadMethodCallException("Method does not exist.");
        }
    }

    public static function register($name, $function)
    {
        self::$registry += [$name => $function];

        self::$boundTwigInstance->addFunction(new \Twig\TwigFunction(
            $name, $function
        ));
    }
}