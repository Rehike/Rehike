<?php
namespace YukisCoffee\CoffeeRequest\Util;

use Exception;

/**
 * Hack to make Promise resolve and reject calls exit the Promise handler.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class PromiseHandlerPrematureReturnException extends Exception
{
    public static self $instance;

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function __construct() {}
}

PromiseHandlerPrematureReturnException::$instance =
    new PromiseHandlerPrematureReturnException();