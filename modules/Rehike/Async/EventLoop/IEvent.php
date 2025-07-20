<?php
namespace Rehike\Async\EventLoop;

/**
 * Declares a common interface for a fulfillable event.
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