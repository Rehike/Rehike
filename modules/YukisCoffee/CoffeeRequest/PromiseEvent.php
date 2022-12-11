<?php
namespace YukisCoffee\CoffeeRequest;

use YukisCoffee\CoffeeRequest\Exception\GeneralException;

use Exception;
use Generator;
use ReflectionFunction;

/**
 * Implements a simple event wrapper for any event that interacts
 * with a Promise (i.e. most events).
 * 
 * @template T
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class PromiseEvent/*<T>*/ extends Event 
{
    /** @var Promise<T> */
    private Promise/*<T>*/ $promise;

    use Deferred { getPromise as public; }

    public function __construct()
    {
        $this->initPromise();
    }

    /**
     * Create a new PromiseEvent from a previously established Promise.
     * 
     * @param Promise<T> $promise
     * @return PromiseEvent<T>
     */
    public static function fromPromise(Promise/*<T>*/ $p): PromiseEvent/*<T>*/
    {
        $self = new static/*<T>*/();

        $self->promise = $p;

        return $self;
    }

    /**
     * Create a new PromiseEvent from an anonymous Promise.
     * 
     * This will not accept a non-generator callback, which is handled
     * in Promise::__construct(). Generator functions return
     * Generator objects after being ran, but they are not Generators
     * by default and it will cause a hang.
     * 
     * @internal
     * 
     * @param Promise<T> $p
     * @param callable<Generator<T>> $cb
     * @param callable<T> $res Resolve API
     * @param callable<Exception|string> $rej Reject API
     * 
     * @return PromiseEvent<T>
     */
    public static function fromAnonPromise(
            Promise/*<T>*/ $p,
            callable/*<Generator<T>>*/ $cb,
            callable/*<T>*/ $res,
            callable/*<Exception|string>*/ $rej
    ): PromiseEvent/*<T>*/
    {
        if (!(new ReflectionFunction($cb))->isGenerator())
        {
            throw new GeneralException(
                "Anonymous promise must be constructed from a " .
                "generator. Add \"if (false) yield;\" to your function" .
                "or update the external handler."
            );
        }

        return new class($p, $cb, $res, $rej) extends PromiseEvent/*<T>*/ {
            /**
             * Callback hack.
             * 
             * PHP actually doesn't allow class members to be typed
             * with callable at all, unlike C# with delegate or
             * TypeScript with its arrow-function-like syntax.
             *  
             * @var callable<Generator<T>> 
             */
            private $onRunCb;

            /** @var callable<T> */
            private $resolveApi;

            /** @var callable<Exception|string> */
            private $rejectApi;

            /**
             * @param Promise<T> $p
             * @param Generator<T> $cb
             * @param callable<T> $res
             * @param callable<Exception|string> $rej
             */
            final public function __construct(
                    Promise/*<T>*/ $p,
                    callable/*<Generator<T>>*/& $cb,
                    callable/*<T>*/ $res,
                    callable/*<Exception|string>*/ $rej
            )
            {
                parent::__construct();

                $this->promise = $p;
                $this->onRunCb = &$cb;

                // Wrap the internal Promise APIs so that they automatically
                // fulfill the Event upon being called.
                $this->resolveApi = self::wrapPromiseApi(
                    $this, $res
                );
                $this->rejectApi = self::wrapPromiseApi(
                    $this, $rej
                );

                Loop::addEvent($this);
            }

            final protected function onRun(): Generator/*<T>*/
            {
                return ($this->onRunCb)($this->resolveApi, $this->rejectApi);
            }
        };
    }

    /**
     * Wrap a Promise's API to also fulfill the event.
     * 
     * This is useful for anonymous Promises, so that they don't
     * need to directly interface with the Event API.
     * 
     * @param PromiseEvent<T> $myself
     * @param callable<mixed> $api
     * @return callable<mixed>
     */
    protected static function wrapPromiseApi(
            PromiseEvent/*<T>*/ $myself,
            callable/*<mixed>*/ $api
    ): callable/*<mixed>*/
    {
        /** @param mixed[] $args */
        return function (...$args) use ($myself, $api) {
            $api(...$args);
            $myself->fulfill();
        };
    }
}