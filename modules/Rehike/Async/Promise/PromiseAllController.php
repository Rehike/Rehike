<?php
namespace Rehike\Async\Promise;

use Rehike\Async\Promise;
use Rehike\Async\IPromise;
use Rehike\Async\Deferred;

use Rehike\Async\Exception\PromiseAllException;

use Exception;
use Generator;
use Rehike\Async\EventLoop\EventFlags;
use Rehike\Async\EventLoop\EventLoop;
use Rehike\Attributes\Override;
use Throwable;

/**
 * Used as a base class for Promise::all() implementation.
 * 
 * This is used to coalesce multiple promises (i.e. mutually dependent ones)
 * into one single Promise with a response array. Compare with ECMAScript's
 * Promise.all().
 * 
 * Use ::getPromise() on an instance, as this is a deferred API and you
 * don't want to return the Promise controller.
 * 
 * This will take in any promise implementation, as long as it implements
 * IPromise. This is to ensure that the Promise::all API is compatible with
 * custom promise implementations.
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PromiseAllController extends PromiseEvent
{
    /** 
     * Stores an array of promises to await.
     * 
     * @var IPromise<mixed>[]
     */
    private array $promises = [];

    /**
     * Stores the number of promises in the bound array.
     */
    private int $boundPromiseCount = 0;

    /**
     * Keeps track of the number of promise resolutions so far.
     * 
     * This is used as a simple check to see if we've finished yet.
     */
    private int $resolvedPromises  = 0;

    /** @param IPromise[] $promises */
    public function __construct(array $promises)
    {
        parent::__construct();
        
        // PATCH (isabella): Taniko's original implementation just made this a free-standing deferred-
        // backed promise controller, which did not participate in the event loop. This implementation
        // caused some weird race condition problems, so I moved it to a self-registered PromiseEvent-
        // backed controller, like all anonymous promises are. To be completely honest, I am not sure
        // why exactly this distinction even exists. It's a little bit entrenched into the design of
        // existing Rehike components, but everything would work just fine if it just relied on
        // anonymous promises.
        EventLoop::addEvent($this);
        
        $this->promises = $promises;

        // Used to track the number of responses internally.
        $this->boundPromiseCount = count($promises);
        
        if ($this->boundPromiseCount == 0)
        {
            // PATCH (isabella): If we didn't have any promises to wait for, then we would create
            // a stale promise which would unravel the execution of the entire program.
            $this->fulfill();
            $this->resolve();
            return;
        }

        $this->awaitPromiseArray($promises);
    }

    /**
     * Handle any promise's resolution.
     */
    public function handlePromiseResolution(IPromise $p): void
    {
        $this->resolvedPromises++;

        // Also check if this is the last value, and if so, resolve.
        // Since the number of promises for one Promise.all aggregator
        // cannot change, this logic is perfectly fine to use.
        if ($this->resolvedPromises == $this->boundPromiseCount)
        {
            $this->resolve($this->getResult());
            $this->fulfill();
        }
    }

    /**
     * Handle any promise's rejection.
     * 
     * Just like with ECMAScript's Promise.all implementation, any promise
     * in the chain failing will result in the entire aggregate Promise
     * being rejected.
     */
    public function handlePromiseRejection(IPromise $p): void
    {
        // Find the index of the given Promise in the array.
        $index = (string)array_search($p, $this->promises) 
            || "(unknown index :/)";

        $message = $p->reason->getMessage();

        $this->fulfill();
        $this->reject(
            new PromiseAllException(
                "Promise $index rejected in Promise::all() call " .
                "with reason \"$message\"",
                $p->reason
            )
        );
    }
    
    #[Override]
    final protected function onRun(): Generator
    {
        // In reality, there's no work to do at all. awaitPromiseArray does all the heavy lifting,
        // including managing the event. This is just necessary to fulfill the PromiseEvent type
        // contract.
        while (true) yield;
    }
    
    #[Override]
    final public function getEventFlags(): int
    {
        // Obviously, Promise::all relies on its child promises to excute, so our event must not
        // block the event loop.
        return EventFlags::AllowQueuedPromisesToPass;
    }

    /**
     * Get the result (contents) of each Promise after they're finished.
     * 
     * @return mixed[]
     */
    private function getResult(): array
    {
        $results = [];

        foreach ($this->promises as $key => $promise)
        {
            $results[$key] = $promise->result;
        }

        return $results;
    }
    
    /**
     * Await an array of Promises and return their responses in
     * an array following the input order.
     * 
     * @param IPromise[] $promises
     */
    private function awaitPromiseArray(array $promises): void
    {
        foreach ($promises as $promise)
        {
            $promise
                ->then(function($result) use ($promise) {
                    $this->handlePromiseResolution($promise);
                })
                ->catch(function(Throwable $e) use ($promise) {
                    $this->handlePromiseRejection($promise);
                })
            ;
        }
    }
}