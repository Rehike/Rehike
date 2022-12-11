<?php
namespace YukisCoffee\CoffeeRequest;

use YukisCoffee\CoffeeRequest\Exception\GeneralException;

use Exception;

/**
 * A template for a Promise controller.
 * 
 * @template T
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
trait Deferred/*<T>*/
{
    /** @var Promise<T> */
    private Promise/*<T>*/ $promise;

    /**
     * Initialise the class's Promise.
     */
    private function initPromise(): void
    {
        $this->promise = new Promise/*<T>*/();
    }

    /**
     * Get this class's Promise.
     * 
     * @return Promise<T>
     */
    private function getPromise(): Promise/*<T>*/
    {
        if (!isset($this->promise))
        {
            throw self::getNoPromiseException();
        }

        return $this->promise;
    }

    /**
     * Resolve the Promise controlled by this class.
     * 
     * @param mixed $data
     */
    private function resolve($data = null): void
    {
        if (!isset($this->promise))
        {
            throw self::getNoPromiseException();
        }

        $this->promise->resolve($data);
    }

    /**
     * Reject the Promise controlled by this class.
     * 
     * @param Exception|string $reason
     */
    private function reject($reason): void
    {
        if (!isset($this->promise))
        {
            throw self::getNoPromiseException();
        }

        $this->promise->reject($reason);
    }

    /**
     * Thrown when a Deferred class doesn't have an initialised
     * Promise.
     */
    private static function getNoPromiseException()
    {
        $class = static::class;

        return new GeneralException(
            "Deferred class $class::\$promise is not a Promise. " .
            "Did you forget to run initPromise()?"
        );
    }
}