<?php
namespace Rehike\Async\EventLoop;

use Rehike\Async\Debugging\PromiseStackTrace;

use Generator;
use const E_USER_WARNING;
use function trigger_error;

/**
 * Represents an asynchronous event.
 * 
 * Events operate based on PHP's native generators. This allows a
 * function to be paused and continued at any time, meaning that an
 * event can be interrupted and yield control to other concurrently
 * running events. As such, singlethreaded asynchronity is achieved.
 * 
 * By themselves, events don't really serve much of a purpose. State
 * cannot be easily communicated between events or to outside areas.
 * Events only serve as a simple proxy for functions that can be paused
 * and resumed within a loop.
 * 
 * The Promise API exists to extend the functionality of an event. With
 * promises, callbacks can be bound and state can be transported, just
 * like a regular function. This paradigm is very reminiscent of
 * ECMAScript's functionality, except that our event loop is blocking and
 * does not necessarily let synchronous functions continue executing in
 * between events.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class Event implements IFulfillableEvent
{
    /**
     * Stores whether or not the event is fulfilled.
     * 
     * An event ceases to be called upon being fulfilled and is
     * eventually culled from the event loop to free memory.
     */
    protected bool $fulfilled = false;

    /**
     * A reference to the Generator callback of onRun().
     * 
     * All future calls to the event will progress this generator,
     * rather than reset it. Of course we don't wanna do that!
     * 
     * @var Generator<void>
     */
    protected Generator/*<void>*/ $generator;

    /**
     * Keeps track of the developer warning.
     * 
     * @see __destruct()
     */
    protected static bool $echoedDeveloperWarning = false;

    /**
     * Run the event.
     */
    final public function run($reset = false): void
    {
        // If the generator is not running, then start the generator
        // and mark it as running. Otherwise, progress the currently
        // running generator.
        if (!isset($this->generator) || $reset)
        {
            $this->generator = $this->onRun();

            // The generator must be rewinded, otherwise events have a
            // tendency to give results prematurely and essentially run
            // twice for the first call.
            $this->generator->rewind();
        }
        else
        {
            $this->generator->next();
        }
    }

    /**
     * Function to be ran every time the event is ran.
     * 
     * Well, technically, only the first time the event is ran...
     * 
     * This is the handler method that all children must override.
     * run() only implements the public API, which requires a hack
     * to keep the Generator running.
     * 
     * @return Generator<void>
     */
    abstract protected function onRun(): Generator/*<void>*/;

    /**
     * Check if the event is fulfilled.
     */
    public function isFulfilled(): bool
    {
        return $this->fulfilled;
    }

    /**
     * End the event and mark it as resolved.
     */
    protected function fulfill(): void
    {
        $this->fulfilled = true;
    }

    /**
     * Destructor hack: report programmer error!
     * 
     * The idea behind this is simple: Events are managed within the
     * event loop, so if they are destructed while the event loop is
     * paused, then it is reasonable to assume the destructor was called
     * by the PHP script ending during its usual cleanup process.
     * 
     * Thus, the programmer can be alerted of their error.
     */
    public function __destruct()
    {
        // If this is the case, then another crash has already occurred
        // and it's pointless to echo.
        if (!class_exists("Rehike\\Async\\EventLoop\\EventLoop")) return;

        if (
            EventLoop::isPaused() && !$this->isFulfilled()
            && !self::$echoedDeveloperWarning
        )
        {
            self::$echoedDeveloperWarning = true;

            trigger_error( 
                "There are currently active events, but the event loop " .
                "is on an endless pause. " .
                "Did you forget to unpause your loop?",
                E_USER_WARNING
            );
        }
    }
}

PromiseStackTrace::registerSkippedFile(__FILE__);