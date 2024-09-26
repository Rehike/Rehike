<?php
namespace YukisCoffee\CoffeeRequest\Util;

use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Loop;
use YukisCoffee\CoffeeRequest\Enum\PromiseStatus;

/**
 * Stores a Promise and its state to be called at a later time.
 * 
 * This allows a Promise resolution or rejection to be delayed. This is used
 * internally by the Event Loop API for memory management.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class QueuedPromiseResolver
{
    /**
     * Stores the Promise to queue.
     */
    private Promise $promise;

    /**
     * Stores the state to set.
     * 
     * This reuses the future-tense PromiseStatus enum. Only
     * RESOLVED and REJECTED may be used for this.
     */
    private $state = PromiseStatus::RESOLVED;

    /**
     * Data to resolve or reject with.
     * 
     * Naturally, this can be of any type, but it must abide by the
     * type constraints of the actual functions.
     * 
     * @var mixed
     */
    private $data;

    /**
     * @param PromiseStatus $state
     * @param mixed $data
     */
    public function __construct(Promise $p, int $state, $data)
    {
        $this->promise = $p;
        $this->state = $state;
        $this->data = $data;
    }

    public function finish(): void
    {
        switch ($this->state)
        {
            case PromiseStatus::RESOLVED:
                $this->promise->resolve($this->data);
                return;
            case PromiseStatus::REJECTED:
                $this->promise->reject($this->data);
                return;
        }
    }
}