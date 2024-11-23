<?php
namespace Rehike\Async;

use Rehike\Async\Promise\{
    PromiseStatus,
    PromiseEvent,
    QueuedPromiseResolver,
    PromiseResolutionTracker,
};

use Rehike\Async\EventLoop\EventLoop;

use Rehike\Async\Exception\UncaughtPromiseException;
use Rehike\Async\Promise\PromiseAllController;
use Rehike\Async\Debugging\PromiseStackTrace;

use Throwable;
use Exception;
use ReflectionFunction;
use ReflectionMethod;

/**
 * A simple Promise implementation for PHP.
 * 
 * Due to the lack of any native event loop in PHP, and thus the reliance 
 * on cURL's own event loop implementation, this is essentially an 
 * overglorified pubsub system.
 * 
 * I haven't tested interoperability with proper asynchronous libraries,
 * i.e. Amp or ReactPHP, so I don't know how compatible this is.
 * 
 * @implements IPromise<T>
 * 
 * @template T
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Promise/*<T>*/ implements IPromise/*<T>*/
{
    /**
     * Represents the current status of a Promise.
     * 
     * @var PromiseStatus 
     */
    public int $status = PromiseStatus::PENDING;

    /** 
     * The result of the Promise after it is finished.
     * 
     * Although this will rarely be manually accessed upon a Promise's
     * resolution, its value is public for internal use and the rare
     * edge cases that manual access is used.
     * 
     * @var T 
     */
    public /*T*/ $result;

    /**
     * The reason for rejecting the Promise, if it is rejected.
     * 
     * Ditto $result for publicity.
     */
    public Throwable $reason;

    /**
     * The stack trace of the Promise at its creation.
     */
    public PromiseStackTrace $creationTrace;

    /**
     * The stack trace of the Promise as of the last update.
     */
    public PromiseStackTrace $latestTrace;

    /** 
     * Callbacks to be ran when the promise is resolved.
     * 
     * Although this is an array, only one callback can be assigned
     * to a Promise. Extra thens are delegated to a subpromise.
     * 
     * Essentially, 
     *     $promiseA->then(getPromiseB())->then(...)
     * is made to be syntatic sugar for:
     *     $promiseA->then(getPromiseB()->then(...))
     * using this array.
     * 
     * As such, extra thens will simply be ignored for any callback
     * that doesn't return a viable Promise.
     * 
     * @var callable<mixed>[] 
     */
    private array $thens = [];

    /**
     * Callbacks to be ran when an error occurs.
     * 
     * The same reason for being an array that apply to thens apply
     * to this as well.
     * 
     * This is actually an associative array for ease of use, so some
     * indices may be skipped. Just be careful with this thing, alright?
     * 
     * @var callable<Throwable>|null[]
     */
    private array $catches = [];

    /**
     * Deferred promise rejections.
     * 
     * This handles cases where rejection catch callbacks are registered after
     * a Promise has already been rejected.
     * 
     * @var ?Throwable[]
     */
    private array $deferredRejections = [];

    /**
     * Requires the Promise to be resolved before the script ends.
     */
    private bool $throwOnUnresolved = true;

    /**
     * Version of the Promise behavior to use.
     */
    private int $version = 0;

    /**
     * Used for keeping track of the current Promise callback level.
     * 
     * That means this increments before every callback and decrements
     * afterwards. This is used for internal checks if the program is
     * currently in a Promise.
     */
    private static int $promiseCallbackLevel = 0;

    /**
     * Create a new anonymous Promise.
     * 
     * A callback can optionally be provided, which will be
     * evaluated.
     * 
     * Unlike a Deferred class's Promise, an anonymous Promise is
     * automatically made into an Event and added to the event loop.
     * 
     * @param ?callable<void> $a
     */
    public function __construct(
            ?callable/*<void>*/ $cb = null, 
            ?array $options = null
    )
    {
        $isCritical = 
            isset($options["critical"]) ? $options["critical"] : true;

        if (null != $cb)
        {
            $reflection = new ReflectionFunction($cb);

            // Since we're an anonymous Promise, we always use the latest
            // version of the Promise behavior. The anonymous Promise backend
            // code is all familiar with the conventions, so it's fine.
            $this->setVersion(0);

            if ($reflection->isGenerator())
            {
                PromiseEvent::fromAnonPromise(
                    $this, 
                    $cb,
                    $this->getResolveApi(),
                    $this->getRejectApi()
                );
            }
            else
            {
                $cb($this->getResolveApi(), $this->getRejectApi());
            }
        }

        $this->throwOnUnresolved = $isCritical;

        $this->creationTrace = new PromiseStackTrace;
        $this->latestTrace = &$this->creationTrace;
    }

    /**
     * Handles promise destruction:
     */
    public function __destruct()
    {
        if (count($this->deferredRejections) > 0)
        {
            foreach ($this->deferredRejections as $rejection)
            {
                if (null != $rejection)
                {
                    throw UncaughtPromiseException::from($rejection);
                }
            }
        }
    }

    /**
     * Sets the version of the Promise behavior.
     * 
     * Different versions can influence how which behavior works. The promise
     * version does not matter for anonymous Promises, which always use the
     * latest version, but they might matter for manual Promises (i.e. in a
     * Deferred class).
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * API function to await an array of Promises, and then
     * return a new Promise with its values.
     * 
     * @param IPromise<mixed>[]|...IPromise $promises
     * @return Promise<mixed[]>
     */
    public static function all(...$promises): Promise/*<array>*/
    {
        // Allow passing an array rather than rest syntax.
        if (is_array($promises[0]))
        {
            $promises = $promises[0];
        }

        return (new PromiseAllController($promises))->getPromise();
    }

    /**
     * Check if the script is currently in a Promise callback.
     */
    public static function isCurrentlyInPromise(): bool
    {
        return self::$promiseCallbackLevel > 0;
    }

    /**
     * Register a function to be called upon a Promise's
     * resolution.
     * 
     * @param callable<mixed>
     * @return Promise<T>
     */
    public function then(callable/*<mixed>*/ $cb): Promise/*<T>*/
    {
        $this->thens[] = $cb;

        // Enable late binding (i.e. for internal versatility)
        if (PromiseStatus::RESOLVED == $this->status)
        {
            self::$promiseCallbackLevel++;
            $cb($this->result);
            self::$promiseCallbackLevel--;
        }

        $this->latestTrace = new PromiseStackTrace;

        if (
            count($this->thens) == 1 && $this->throwOnUnresolved &&
            $this->status == PromiseStatus::PENDING
        )
        {
            PromiseResolutionTracker::registerPendingPromise($this);
        }

        return $this;
    }

    /**
     * Register a function to be called upon an error occurring
     * during a Promise's resolution.
     * 
     * @param callable<Throwable> $cb
     * @return Promise<T>
     */
    public function catch(callable/*<Throwable>*/ $cb): Promise/*<T>*/
    {
        $current = $this->getCurrentThenIndex();

        $this->catches[$current] = $cb;

        // Late binding
        if (isset($this->deferredRejections[$current]))
        {
            self::$promiseCallbackLevel++;
            $cb($this->deferredRejections[$current]);
            self::$promiseCallbackLevel--;
        }
        else if (PromiseStatus::REJECTED == $this->status)
        {
            self::$promiseCallbackLevel++;
            $cb($this->reason);
            self::$promiseCallbackLevel--;
        }

        $this->latestTrace = new PromiseStackTrace;

        return $this;
    }

    /**
     * Resolve a Promise (and call its thens).
     * 
     * @internal
     * @param T $data
     */
    public function resolve(/*T*/ $data = null): void
    {
        /*
         * A promise's resolution should not be called if the event
         * loop is still running.
         * 
         * Instead, it is added to a queue to be called upon the loop's
         * cleanup. This flattens the call stack and allows GC to get in
         * and clean up memory used in the events.
         * 
         * Otherwise, the resolution callback would be predicated on the
         * event logic and it could cause a memory leak. Only the data
         * ultimately used by the Promise should be left behind.
         */
        if (!EventLoop::isFinished())
        {
            EventLoop::addQueuedPromise(
                new QueuedPromiseResolver(
                    $this,
                    PromiseStatus::RESOLVED,
                    $data
                )
            );

            return; // Should finish here.
        }

        $this->result = $data;

        /*
         * Do nothing if the Promise already has a set status.
         * 
         * This is important, as without it, a rejection call followed
         * by a resolution call will be handled as if it's resolved,
         * thus causing odd behaviour.
         */
        if (PromiseStatus::PENDING != $this->status) return;

        // If there's nothing to resolve, do nothing
        if (($count = count($this->thens)) > 0)
        {
            self::$promiseCallbackLevel++;
            $result = $this->thens[0]($data);
            self::$promiseCallbackLevel--;

            // If the response itself is a Promise, bind any
            // further existing thens to it (this process will
            // repeat itself in the next resolution).
            if ($count > 1 && $result instanceof Promise/*<T>*/)
            {
                for ($i = 1; $i < $count; $i++)
                {
                    $result->then($this->thens[$i]);
                }
            }
        }

        if ($this->throwOnUnresolved)
        {
            PromiseResolutionTracker::unregisterPendingPromise($this);
        }

        $this->setStatus(PromiseStatus::RESOLVED);
    }

    /**
     * Reject a Promise (error).
     * 
     * @param string|Throwable $e (union types are PHP 8.0+)
     * 
     * @internal
     * @param 
     */
    public function reject($e): void
    {
        /*
         * A promise's rejection should not be called if the event
         * loop is still running.
         * 
         * Instead, it is added to a queue to be called upon the loop's
         * cleanup. This flattens the call stack and allows GC to get in
         * and clean up memory used in the events.
         * 
         * Otherwise, the rejection callback would be predicated on the
         * event logic and it could cause a memory leak. Only the data
         * ultimately used by the Promise should be left behind.
         */
        if (!EventLoop::isFinished())
        {
            EventLoop::addQueuedPromise(
                new QueuedPromiseResolver(
                    $this,
                    PromiseStatus::REJECTED,
                    $e
                )
            );

            return; // Should finish here.
        }

        /*
         * Do nothing if the Promise already has a set status.
         * 
         * This is important, as without it, a rejection call followed
         * by a resolution call will be handled as if it's resolved,
         * thus causing odd behaviour.
         */
        if (PromiseStatus::PENDING != $this->status) return;

        if (is_string($e))
        {
            $this->reason = new Exception($e);
        }
        else if ($e instanceof Throwable)
        {
            $this->reason = $e;
        }

        // If there's nothing to reject, do nothing
        $current = $this->getCurrentThenIndex();

        if (isset($this->catches[$current]))
        {
            // If we already have a catch registered at the time of rejection,
            // then we can simply register now:
            self::$promiseCallbackLevel++;
            $this->catches[$current]($this->reason);
            self::$promiseCallbackLevel--;
        }
        else
        {
            // Otherwise, if the corresponding catch is not registered, then we
            // want to defer the registration:
            $this->deferredRejections[$current] = $this->reason;
        }

        if ($this->throwOnUnresolved)
        {
            PromiseResolutionTracker::unregisterPendingPromise($this);
        }

        $this->setStatus(PromiseStatus::REJECTED);
    }

    /**
     * Get a proxy to an internal API handler here, such as resolve()
     * or reject().
     * 
     * @internal
     */
    protected function getInternalApi(string $name): callable/*<mixed>*/
    {
        return function (...$args) use ($name) {
            $this->{$name}(...$args);
        };
    }

    /**
     * Get the internal resolution API.
     * 
     * @see resolve()
     */
    protected function getResolveApi(): callable/*<mixed>*/
    {
        return $this->getInternalApi("resolve");
    }

    /**
     * Get the internal rejection API.
     * 
     * @see reject()
     */
    protected function getRejectApi(): callable/*<string|Exception>*/
    {
        return $this->getInternalApi("reject");
    }

    /**
     * Set the Promise's status to a new value.
     * 
     * The value is technically an int due to the current enum
     * implementation, however the type should always be a value
     * within the PromiseStatus enum.
     * 
     * @param PromiseStatus $newStatus
     */
    protected function setStatus(int $newStatus): void
    {
        $this->status = $newStatus;
    }

    /**
     * Get the current number of thens bound to a single Promise.
     * 
     * This is used internally to determine the correct child Promise
     * to bind a exception handler (->catch) to.
     */
    protected function getCurrentThenIndex(): int
    {
        return count($this->thens) - 1;
    }
}

PromiseStackTrace::registerSkippedFile(__FILE__);