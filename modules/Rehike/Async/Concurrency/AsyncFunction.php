<?php
namespace Rehike\Async\Concurrency;

use Generator, Exception;
use Rehike\Async\Debugging\IObjectWithTrackingCookie;
use Rehike\Async\Debugging\TraceEventId;
use Rehike\Async\Debugging\Tracing;
use Rehike\Async\Debugging\TrackingCookie;
use Rehike\Async\EventLoop\EventLoop;
use Throwable;
use Rehike\Async\Promise;

/**
 * Represents an async function in execution.
 * 
 * Async functions work in Rehike by using generator functions that yield
 * promises within them. This class implements the logic necessary to
 * capture the Promise and send back the value of the Promise. 
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class AsyncFunction implements IObjectWithTrackingCookie
{
    /**
     * The generator is the backend of the whole thing.
     * 
     * Generators are a perfect to emulate async functions since their
     * yield functionality is pretty much identical to await.
     * 
     * Most notably, you can capture the result of a yield expression, use
     * it, and then replace it with a completely different variable.
     */
    private Generator $generator;

    /**
     * A Promise for the completion of the async function.
     * 
     * This enables API compatibility with Promise-based code, i.e. you
     * can `myAsyncFunction()->then("handleAsyncResult")`. When the async
     * function returns or throws, this Promise will be resolved or rejected
     * respectively.
     */
    private Promise $ownPromise;
    
    /**
     * A reference to the internal event we use to put the async function onto
     * the event loop.
     */
    private AsyncFunctionEvent $event;

    public static function __initStatic()
    {
        \Rehike\Async\Debugging\PromiseStackTrace::registerSkippedFile(__FILE__);
    }

    public function __construct(Generator $g)
    {
        $this->generator = $g;
        $this->ownPromise = new Promise();
        
        // XXX(isabella): Since async functions are just wrappers for promises, we'll
        // actually just share the tracking cookie with the promise.
        Tracing::logEvent(TraceEventId::AsyncFunctionCreate, $this->ownPromise->getTrackingCookie());
        
        $this->event = new AsyncFunctionEvent($this);
        EventLoop::addEvent($this->event);
    }
    
    public function __destruct()
    {
        Tracing::logEvent(TraceEventId::AsyncFunctionDestroy, $this->getTrackingCookie());
    }
    
    // XXX(isabella): Since async functions are just wrappers for promises, we'll
    // actually just share the tracking cookie with the promise.
    public function getTrackingCookie(): TrackingCookie
    {
        return $this->ownPromise->getTrackingCookie();
    }

    /**
     * Get a Promise representing the state of the async function.
     * 
     * This will resolve or reject accordingly with the value returned
     * or thrown by the async function.
     */
    public function getPromise(): Promise
    {
        return $this->ownPromise;
    }

    /**
     * Run the async function for one iteration.
     * 
     * This progresses the internal Generator and captures the value held
     * within it as long as the Generator has not yet returned.
     * 
     * If the Generator has returned, then it will resolve this function's
     * Promise and message the return status to all internal listeners.
     * Likewise for rejection.
     */
    public function run(): void
    {
        Tracing::logEvent(TraceEventId::AsyncFunctionRun, $this->getTrackingCookie());
        
        // A valid generator is one that has not returned.
        if ($this->generator->valid())
        {
            try
            {
                /*
                 * Capture the value of the generator *and then progress
                 * it*.
                 * 
                 * Those unfamiliar with PHP's generator implementation may
                 * be a little confused with this behaviour. Whenever you
                 * try to get the current value of the generator, it will
                 * return that value and then run the generator again.
                 * 
                 * That's also why this function never explicitly calls
                 * Generator::next().
                 */
                $value = $this->generator->current();
            }
            catch (Throwable $e)
            {
                // Capture the exception thrown by the Generator and carry
                // it over to the internal Promise.
                Tracing::logEvent(TraceEventId::AsyncFunctionCatchUnderlyingException, $this->getTrackingCookie());
                $this->event->fulfill();
                $this->ownPromise->reject($e);
            }
        }
        else // has returned
        {
            Tracing::logEvent(TraceEventId::AsyncFunctionFinishingUp, $this->getTrackingCookie());
            $this->event->fulfill();
            
            try
            {
                $result = $this->generator->getReturn();
                $this->ownPromise->resolve($result);
            }
            catch (Throwable $e)
            {
                // If the promise threw an exception that was caught before
                // getting to us (the network library can do this internally), then
                // it will no have return value and will throw an exception. We
                // just have to ignore it.
                // PATCH (izzy): Resolved issue #682, thanks lemon-pumpkin-pie!
                if ($e instanceof Exception
                    && $e->getMessage() == "Cannot get return value of a generator that hasn't returned"
                )
                {
                    $this->ownPromise->resolve(null);
                    return;
                }

                // Regular exceptions still need to be passed to the async
                // function for handling.
                $this->ownPromise->reject($e);
            }
            
            return;
        }

        // Obviously, an async function must take in a Promise.
        if ($value instanceof Promise)
        {
            $value->then($this->getThenHandler());
            $value->catch($this->getCatchHandler());
        }
        else
        {
            throw new \Exception(
                "An async function must take in a Promise. Note that you may be yielding on an already-unwrapped " .
                "Promise result rather than the Promise itself."
            );
        }
    }

    /**
     * Get a then handler for the internal Promise.
     * 
     * Only one handler is ever needed, which is this closure. This relies
     * on the Promise messaging system to send the value of the current
     * Promise back to the generator and then rerun it.
     */
    protected function getThenHandler(): callable
    {
        return function (mixed $value) {
            $this->generator->send($value);
            $this->run();
        };
    }
    
    /**
     * Get a catch handler for the internal Promise.
     */
    protected function getCatchHandler(): callable
    {
        return function (mixed $value) {
            if ($value instanceof Throwable)
            {
                throw $value;
            }
            else
            {
                throw new \Exception((string)$value);
            }
        };
    }
}