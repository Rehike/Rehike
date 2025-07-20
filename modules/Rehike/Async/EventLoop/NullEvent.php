<?php
namespace Rehike\Async\EventLoop;

use Rehike\Attributes\Override;

/**
 * Represents a null event in memory, i.e. one with nothing bound to it.
 * 
 * This should only ever be used internally to denote a void event within
 * the event loop for a temporary time.
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NullEvent implements IEvent
{
    public function isFulfilled(): bool
    {
        return true;
    }
    
    /**
     * @return EventFlags
     */
    #[Override]
    public function getEventFlags(): int
    {
        // Null events have basically no reason to block the event loop,
        // so they're treated as suspended.
        return EventFlags::Suspended;
    }
    
    #[Override]
    final public function run(bool $reset = false): void
    {
        // Do nothing.
    }
}