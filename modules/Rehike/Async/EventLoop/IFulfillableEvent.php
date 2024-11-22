<?php
namespace Rehike\Async\EventLoop;

/**
 * Declares a common interface for a fulfillable event.
 * 
 */
interface IFulfillableEvent
{
    /**
     * Check if the event is fulfilled.
     */
    public function isFulfilled(): bool;
}