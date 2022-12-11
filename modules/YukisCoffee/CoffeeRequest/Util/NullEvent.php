<?php
namespace YukisCoffee\CoffeeRequest\Util;

/**
 * Represents a null event in memory, i.e. one with nothing bound to it.
 * 
 * This should only ever be used internally to denote a void event within
 * the event loop for a temporary time.
 * 
 * @internal
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NullEvent implements IFulfillableEvent
{
    public function isFulfilled(): bool
    {
        return true;
    }
}