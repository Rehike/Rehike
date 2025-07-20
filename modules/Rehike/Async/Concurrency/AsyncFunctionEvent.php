<?php
namespace Rehike\Async\Concurrency;

use Generator;
use Rehike\Async\EventLoop\Event;
use Rehike\Async\EventLoop\EventFlags;
use Rehike\Attributes\Override;

/**
 * Wrapper to put async functions onto the event loop.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class AsyncFunctionEvent extends Event
{
    private AsyncFunction $asyncFunction;
    
    public function __construct(AsyncFunction $func)
    {
        parent::__construct();
        $this->asyncFunction = $func;
    }
    
    #[Override]
    public function fulfill(): void
    {
        parent::fulfill();
    }
    
    #[Override]
    final protected function onRun(): Generator
    {
        while (true)
        {
            yield;
        }
    }
    
    #[Override]
    final public function getEventFlags(): int
    {
        // Since we rely on the execution of promises, this event should not block
        // queued promise resolution.
        return EventFlags::AllowQueuedPromisesToPass;
    }
}