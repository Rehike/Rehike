<?php
namespace YukisCoffee\CoffeeRequest\Util;

use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Deferred;
use YukisCoffee\CoffeeRequest\Exception\PromiseAllException;

use Exception;

/**
 * Used as a base class for Promise::all() implementation.
 * 
 * This is used to coalesce multiple Promises (i.e. mutually dependent ones)
 * into one single Promise with a response array. Compare with ECMAScript's
 * Promise.all().
 * 
 * Use ::getPromise() on an instance, as this is a deferred API and you
 * don't want to return the Promise controller.
 * 
 * @internal
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class PromiseAllBase
{
    use Deferred/*<mixed[]>*/ { getPromise as public; }

    /** 
     * Stores an array of Promises to await.
     * 
     * @var Promise<mixed>[]
     */
    private array $promises = [];

    /**
     * Stores the number of Promises in the bound array.
     */
    private int $boundPromiseCount = 0;

    /**
     * Keeps track of the number of Promise resolutions so far.
     * 
     * This is used as a simple check to see if we've finished yet.
     */
    private int $resolvedPromises  = 0;

    /** @param Promise[] $promises */
    public function __construct(array $promises)
    {
        $this->initPromise();
        $this->promises = $promises;

        // Used to track the number of responses internally.
        $this->boundPromiseCount = count($promises);

        $this->awaitPromiseArray($promises);
    }

    /**
     * Handle any Promise's resolution.
     */
    public function handlePromiseResolution(Promise $p): void
    {
        $this->resolvedPromises++;

        // Also check if this is the last value, and if so, resolve.
        // Since the number of Promises for one Promise.all aggregator
        // cannot change, this logic is perfectly fine to use.
        if ($this->resolvedPromises == $this->boundPromiseCount)
        {
            $this->resolve($this->getResult());
        }
    }

    /**
     * Handle any Promise's rejection.
     * 
     * Just like with ECMAScript's Promise.all implementation, any Promise
     * in the chain failing will result in the entire aggregate Promise
     * being rejected.
     */
    public function handlePromiseRejection(Promise $p): void
    {
        // Find the index of the given Promise in the array.
        $index = (string)array_search($p, $this->promises) 
            || "(unknown index :/)";

        $message = $p->reason->getMessage();

        $this->reject(
            new PromiseAllException(
                "Promise $index rejected in Promise::all() call " .
                "with reason \"$message\"",
                $p->reason // exception
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
     * @param Promise[] $promises
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