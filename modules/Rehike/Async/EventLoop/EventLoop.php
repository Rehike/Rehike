<?php
namespace Rehike\Async\EventLoop;

use Rehike\Async\Promise\QueuedPromiseResolver;
use Rehike\Async\Debugging\PromiseStackTrace;

use Exception;

/**
 * Implements the Rehike event loop.
 * 
 * The event loop is very simple, simply checking iterating over the
 * registered events and calling them. Events dictate their own cuts of
 * the runtime, but they may yield at any time and switch execution to
 * another event.
 * 
 * This, along with the Event API, bring to PHP a simple singlethreaded
 * asynchronous execution system.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class EventLoop
{
    /**
     * Stores all current events.
     * 
     * @var IFulfillableEvent[]
     */
    private static array $events = [];

    /**
     * Stores an array of QueuedPromiseResolvers to be finished upon
     * the end of the Event loop.
     * 
     * @var QueuedPromiseResolver[]
     */
    private static array $queuedPromises = [];

    /**
     * Stores the current pause state of the event loop.
     */
    private static bool $isPaused = false;

    /**
     * Keeps track of the current call level.
     */
    private static int $level = 0;

    // Disable instances
    private function __construct() {}

    /**
     * Run the event loop.
     * 
     * Running the event loop will block further code execution, so
     * still think of this as a synchronous operation.
     * 
     * Although this call may be synchronous, what happens with the
     * events isn't, so you can implement your own asynchronous handlers
     * using the Events API if need be.
     */
    public static function run(): void
    {
        self::$level++;

        do
        {
            foreach (self::$events as $event) if (
                !$event->isFulfilled() &&
                $event instanceof Event
            )
            {
                $event->run();
            }
        }
        while (self::shouldContinueRunning());

        // We can't just assume that the event is finished when this
        // function stops being called, as it can also be paused.
        if (self::isFinished())
        {
            self::$level--;
            self::cleanup();
        }
    }

    /**
     * Determine if the event loop has an event.
     */
    public static function hasEvent(Event $e): bool
    {
        return (bool)array_search($e, self::$events);
    }

    /**
     * Add an event to the event loop.
     */
    public static function addEvent(Event $e): void
    {
        self::$events[] = $e;
    }

    /**
     * Add an event to the event loop if it's not already there.
     */
    public static function addEventIfNotAdded(Event $e): void
    {
        if (!self::hasEvent($e))
        {
            self::addEvent($e);
        }
    }

    /**
     * Remove an event from the event loop.
     */
    public static function removeEvent(IFulfillableEvent $e): void
    {
        $index = array_search($e, self::$events);

        if (false != $index)
        {
            array_splice(self::$events, $index, 1);
        }
        else
        {
            throw new Exception(
                "Attempted to remove a non-existent event from the loop."
            );
        }
    }

    /**
     * Reports whether or not the event loop is currently paused.
     */
    public static function isPaused(): bool
    {
        return self::$isPaused;
    }

    /**
     * Check if the loop is finished running.
     * 
     * Unlike checking if the loop should continue running, this does
     * not return true if the event loop is paused.
     */
    public static function isFinished(): bool
    {
        return self::isPaused()
            ? false
            : !self::shouldContinueRunning();
    }

    /**
     * Pauses the event loop.
     * 
     * When the event loop is paused, code declared outside of events
     * continues to execute synchronously until the event loop is
     * manually continued.
     * 
     * Naturally, the event loop can only be paused within an event. Be
     * careful to continue the event loop afterwards. If you make a mistake
     * and the event loop is never continued, a warning will be displayed to
     * notify you of your probable mistake.
     * 
     * This is an advanced feature that has few use cases, but it is
     * supported.
     */
    public static function pause(): void
    {
        self::$isPaused = true;
    }

    /**
     * Continues event loop execution and unpauses the loop.
     */
    public static function continue($autoRun = true): void
    {
        self::$isPaused = false;
        
        if ($autoRun) self::run();
    }

    /**
     * Add a QueuedPromiseResolver to the queue.
     */
    public static function addQueuedPromise(QueuedPromiseResolver $p): void
    {
        self::$queuedPromises[] = $p;
    }

    /**
     * Used internally to determine if the loop is still running.
     */
    private static function shouldContinueRunning(): bool
    {
        if (self::$isPaused) return false;

        foreach (self::$events as &$event) 
        {
            if (
                !$event->isFulfilled() &&
                $event instanceof Event
            )
            {
                return true;
            }
            else if (!@$event->preventNullification)
            {
                // The event is no longer needed at all since it's
                // no longer accessed after being fulfilled. Might as well
                // clean it from memory and get it over with.
                $event = new NullEvent();
            }
        }

        return false;
    }

    /**
     * Clean up the mess this made in memory.
     */
    private static function cleanup(): void
    {
        // Rely on GC to CL34NUP memory afterwards >:]
        self::$events = [];

        // Notify the delayed promise resolutions to finish.
        self::finishQueuedPromises();
    }

    /**
     * Finish the Promise queue after Event memory is freed.
     */
    private static function finishQueuedPromises(): void
    {
        foreach (self::$queuedPromises as $promise)
        {
            $promise->finish();

            if (self::$level == 0 && count(self::$events) > 0)
            {
                self::run();
            }
        }

        // Since all queued Promise callbacks have been gotten to,
        // the queues aren't necessary.
        self::$queuedPromises = [];
    }
}

PromiseStackTrace::registerSkippedFile(__FILE__);