<?php
namespace YukisCoffee\CoffeeRequest;

/**
 * Static wrapper for CoffeeRequestInstance (the new main code as of version 2.0).
 * 
 * This only exists to maintain compatibility with legacy v1.0 code. Please use that.
 * 
 * Slowly, this will probably be deprecated. When the time comes, CoffeeRequestInstance
 * may be removed. As such, do not directly construct CoffeeRequestInstance.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class CoffeeRequest
{
    protected static $staticWrapper;
    protected $instanceWrapper;

    public static function __initStatic()
    {
        CoffeeRequest::_staticWrapperInit();
    }

    // Behaviour for all method calls
    protected static function _methodCallWrapper(&$actor, $name, $args)
    {
        $reflection = new \ReflectionClass($actor);

        if ($reflection->hasMethod($name) && $reflection->getMethod($name)->isPublic())
        {
            return $actor->{$name}(...$args);
        }
        else
        {
            trigger_error("Method does not exist or is invisible.", E_USER_ERROR);
        }
    }

    // These act as static links to all properties of the instance class.
    public static $requestsMaxAttempts;
    public static $defaultOptions;
    public static $defaultHeaders;
    public static $requestQueue;

    public static function _staticWrapperInit()
    {
        self::$staticWrapper = new CoffeeRequestInstance("do not access this directly!");

        self::$requestsMaxAttempts = &self::$staticWrapper->requestsMaxAttempts;
        self::$defaultOptions = &self::$staticWrapper->defaultOptions;
        self::$defaultHeaders = &self::$staticWrapper->defaultHeaders;
        self::$requestQueue = &self::$staticWrapper->requestQueue;
    }

    public static function __callStatic($name, $args)
    {
        return self::_methodCallWrapper(self::$staticWrapper, $name, $args);
    }

    // Also allow this to be used as a "link" for CoffeeRequestInstance
    public function __construct()
    {
        $this->instanceWrapper = new CoffeeRequestInstance("do not access this directly!");
    }

    public function __call($name, $args)
    {
        return self::_methodCallWrapper($this->instanceWrapper, $name, $args);
    }

    public function &__get($variable)
    {
        return $this->instanceWrapper->{$variable};
    }

    public function __set($variable, $value)
    {
        $this->instanceWrapper->{$variable} = $value;
    }
}