<?php
namespace Rehike\Async\EventLoop;

use Exception;
use Rehike\Async\Debugging\PromiseStackTrace;

use Generator;
use Rehike\Async\Debugging\IObjectWithTrackingCookie;
use Rehike\Async\Debugging\TraceEventId;
use Rehike\Async\Debugging\Tracing;
use Rehike\Async\Debugging\TrackingCookie;
use Rehike\Attributes\Override;

use const E_USER_WARNING;
use function trigger_error;

/**
 * Enables the run limit on events in the async framework.
 * 
 * The run limit is a primitive debugging utility which makes it easier to catch
 * if an event is locked up.
 */
const ENABLE_EVENT_RUN_LIMIT = true;

/**
 * Limits the number of times a single event may be ran.
 * 
 * Events in Rehike never even run up to 10,000 times under normal execution, so
 * such a high number may even be considered overkill.
 */
const EVENT_RUN_LIMIT = 32767;

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
abstract class Event implements IEvent, IObjectWithTrackingCookie
{
    /**
     * Stores whether or not the event is fulfilled.
     * 
     * An event ceases to be called upon being fulfilled and is
     * eventually culled from the event loop to free memory.
     * 
     * However, the programmer can manually override automatic management
     * of the events by specifying the MayResetFulfillment flag. In this
     * case, the lifetime of the event is fully-controlled by the programmer.
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
     * Tracking cookie for debug purposes.
     */
    private TrackingCookie $cookie;
    
    /**
     * Tracks the number of times this event has been ran.
     */
    private int $runAmount = 0;
    
    public function __construct()
    {
        // Some children can override the constructor without calling the
        // parent constructor, so we ensure initialisation of any dependencies
        // in multiple locations.
        $this->ensureTrackingCookie();
    }
    
    public function getTrackingCookie(): TrackingCookie
    {
        $this->ensureTrackingCookie();
        return $this->cookie;
    }
    
    private function ensureTrackingCookie(): void
    {
        if (!isset($this->cookie))
        {
            $this->cookie = new TrackingCookie(__CLASS__);
            Tracing::logEvent(TraceEventId::EventCreate, $this->cookie);
        }
    }

    /**
     * Run the event.
     */
    #[Override]
    final public function run(bool $reset = false): void
    {
        $this->ensureTrackingCookie();
        Tracing::logEvent(TraceEventId::EventRun, $this->cookie);
        
        if (ENABLE_EVENT_RUN_LIMIT)
        {
            if ($this->runAmount >= EVENT_RUN_LIMIT && !((int)$this->getEventFlags() & EventFlags::NoRunLimit))
            {
                $this->fulfill();
                
                \Rehike\Logging\DebugLogger::print(
                    __METHOD__.": Stopped event %s in its tracks because it ran %d times",
                    $this->cookie,
                    $this->runAmount
                );
                
                throw new \Exception("An event is locked up.");
            }
        }
        
        $this->runAmount++;
        
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
    
    #[Override]
    public function getEventFlags(): int
    {
        return EventFlags::None;
    }

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
        Tracing::logEvent(TraceEventId::EventFulfill, $this->cookie);
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
        Tracing::logEvent(TraceEventId::EventDestroy, $this->cookie);
        
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