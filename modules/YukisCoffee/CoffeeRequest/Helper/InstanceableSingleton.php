<?php
namespace YukisCoffee\CoffeeRequest\Helper;

use YukisCoffee\CoffeeRequest\Helper\SingletonUtils as Utils;

use Exception;

/**
 * A singleton that can be instantiated, just as a regular class.
 * 
 * Class instances created through this class are also proxied, so
 * the same API considerations (make singletons final) that apply
 * to static-only singletons apply to instanceable ones too.
 * 
 * @template T
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class InstanceableSingleton/*<T>*/ extends Singleton/*<T>*/
{
    /** 
     * Refers to the current proxy instance's true instance.
     * 
     * $instance still refers to static::$instance.
     * 
     * @var T 
     */
    private $thisInstance;

    /**
     * As these are constructable, there needs to be a public
     * constructor.
     */
    public function __construct(...$args)
    {
        $T = static::T;

        $this->thisInstance = new $T(...$args);
    }

    /** @return mixed */
    public function &__get(string $name)
    {

    }

    /** @param mixed $value */
    public function __set(string $name, $value): void
    {

    }

    /**
     * @param mixed[] $args
     * @return mixed
     */
    public function __call(string $name, $args)
    {
        $T = static::T;

        try
        {
            return Utils/*<T>*/::call($T, self::$instance, $name, $args);
        }
        catch (Exception $e)
        {
            // This must be elevated to the caller's scope.
            throw $e;
        }
    }
}