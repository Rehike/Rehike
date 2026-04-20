<?php
namespace Rehike\Async\EventLoop;

/**
 * Declares a common interface for a fulfillable event.
 * 
 * This interface is an implementation detail and you should not supply a custom
 * class which implements it. Instead, extend from the {@see Event} class.
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IEvent
{
    /**
     * Check if the event is fulfilled.
     */
    public function isFulfilled(): bool;
    
    /**
     * Gets runtime flags for this event.
     * 
     * @return EventFlags
     */
    public function getEventFlags(): int;
    
    /**
     * Run the event.
     */
    public function run(bool $reset = false): void;
}