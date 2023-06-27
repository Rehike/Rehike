<?php
namespace YukisCoffee\CoffeeRequest\Util;

/**
 * Declares a common interface for a fulfillable event.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IFulfillableEvent
{
    /**
     * Check if the event is fulfilled.
     */
    public function isFulfilled(): bool;
}