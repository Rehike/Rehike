<?php
namespace Rehike;

/**
 * Implement the template function container.
 * 
 * This was created primarily to move away from the
 * previous solution, which polluted the global scope with
 * reference variables to the template functions (which are sometimes
 * used within PHP for convenience, think a shared function).
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class TemplateFunctions
{
    /** 
     * Registry of template functions
     * 
     * @var callback[]
     */
    protected static $registry = [];

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

    /**
     * Register a template function.
     * 
     * @param string $name of the function
     * @param callback $function
     */
    public static function register($name, $function)
    {
        self::$registry += [$name => $function];

        TemplateManager::addFunction($name, $function);
    }
}