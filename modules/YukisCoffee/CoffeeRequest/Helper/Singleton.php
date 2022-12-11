<?php
namespace YukisCoffee\CoffeeRequest\Helper;

use YukisCoffee\CoffeeRequest\Helper\SingletonUtils as Utils;

use Exception;

/**
 * Statically proxy an instance class.
 * 
 * This is used in cases where the advantages of an instance class are
 * desired, but the instance must be accessed globally instead.
 * 
 * Basically MyClass::hello() will correspond to (new MyClass())->hello(),
 * except that the anonymous instance is permanent.
 * 
 * These singletons are simple, only proxying methods. If a variable
 * property needs to be accessed, the instance can be accessed through
 * the $instance property. For example:
 * 
 * <code>
 *      MyClass::$instance->myProperty = 1;
 * </code>
 * 
 * A general API consideration you should make is marking any Singleton
 * class as final, as to prevent a misextension. Since they're proxy
 * classes, they don't truly implement the properties of the classes
 * that they proxy.
 * 
 * @template T
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class Singleton/*<T>*/
{
    /**
     * Type of the bound singleton.
     * 
     * For ease of access, this can generally point to the ::class
     * pseudo-const of any existing class, i.e. stdClass::class.
     * 
     * This is also the closest PHP currently gets to true generics.
     * 
     * @var string
     * @internal
     */
    protected const T = stdClass::class;

    /**
     * Stores the parent class for static use.
     * 
     * @var T
     */
    public static $instance;

    /**
     * A singleton proxy should not be constructable.
     * 
     * Protected inherits this non-constructability to child classes
     * too, unless they explicitly override the protected constructor
     * with a public one.
     */
    protected function __construct() {}

    /**
     * Initialise the singleton for the first time.
     */
    private static function initSingleton(): void
    {
        $T = static::T;

        // Cannot use a constant as an interpolated class constructor!
        self::$instance = new $T();
    }

    /**
     * Call a method on the bound instance.
     * 
     * @param mixed[] args
     * @return mixed
     */
    public static function __callStatic(string $name, array $args)
    {
        $T = static::T;

        if (!(self::$instance instanceof $T))
        {
            self::initSingleton();
        }

        try
        {
            return Utils/*<T>*/::call($T, self::$instance, $name, $args, true);
        }
        catch (Exception $e)
        {
            // This must be elevated to the caller's scope.
            throw $e;
        }
    }
}