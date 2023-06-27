<?php
namespace YukisCoffee\CoffeeRequest\Handler;

use YukisCoffee\CoffeeRequest\Event;

/**
 * This is a factory class that is used internally to determine
 * the best default network handler to use.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NetworkHandlerFactory
{
    /**
     * Determine the best type of factory to use by analysing a
     * specific configuration.
     */
    public static function getBest(): NetworkHandler
    {
        switch (true)
        {
            case self::isCurlSupported():
                return self::getCurl();
            default:
                return self::getNull();
        }
    }

    /**
     * Return the cURL-based handler.
     */
    public static function getCurl(): CurlHandler
    {
        return new CurlHandler();
    }
    
    /**
     * Determine if the cURL extension is installed on the system.
     */
    public static function isCurlSupported(): bool
    {
        return function_exists("\curl_init");
    }

    /**
     * Return a null handler in the event there's no supported
     * handler installed to use.
     */
    public static function getNull(): NullHandler
    {
        return new NullHandler();
    }
}