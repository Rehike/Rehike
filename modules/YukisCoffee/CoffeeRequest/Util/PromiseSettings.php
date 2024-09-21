<?php
namespace YukisCoffee\CoffeeRequest\Util;

/**
 * Global settings for Promises in CoffeeRequest.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class PromiseSettings
{
    private static bool $enableHandlerPrematureReturn = true;

    /**
     * Get if premature returning from Promise handlers is enabled.
     * 
     * Premature returning status affects runtime behavior. If it is disabled,
     * then Promise handlers will continue executing past their resolve or
     * reject calls.
     */
    public static function getEnableHandlerPrematureReturn(): bool
    {
        return self::$enableHandlerPrematureReturn;
    }

    /**
     * Enable or disable premature returning from Promise handlers.
     * 
     * Premature returning status affects runtime behavior. If it is disabled,
     * then Promise handlers will continue executing past their resolve or
     * reject calls.
     */
    public static function setEnableHandlerPrematureReturn(bool $value): void
    {
        self::$enableHandlerPrematureReturn = $value;
    }
}