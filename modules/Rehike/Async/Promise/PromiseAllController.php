<?php
namespace Rehike\Async\Promise;

use Rehike\Async\Promise;
use Rehike\Async\IPromise;
use Rehike\Async\Deferred;

use Rehike\Async\Exception\PromiseAllException;

use Exception;

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
class PromiseAllController
{
    use Deferred/*<mixed[]>*/ { getPromise as public; }

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
        $this->initPromise();
        $this->promises = $promises;

        // Used to track the number of responses internally.
        $this->boundPromiseCount = count($promises);

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

        $this->reject(
            new PromiseAllException(
                "Promise $index rejected in Promise::all() call " .
                "with reason \"$message\"",
                $p->reason
            )
        );
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
                ->catch(function(Exception $e) use ($promise) {
                    $this->handlePromiseRejection($promise);
                })
            ;
        }
    }
}