# Advanced Promises

This article discusses promises in depth, including the implementation of custom promise objects via the [`IPromise`](../IPromise.php) interface, various debugging features, and other implementation details of promises.

[For common purposes, please see the main article on promises.](promises.md)

## Table of contents

1. [Promise resolution tracking](#promise-resolution-tracking)
2. [Queued promise resolution](#queued-promise-resolution)
3. [Implementing a custom promise class](#implementing-a-custom-promise-class)
4. [Promise behaviour versions](#promise-behaviour-versions)
5. [Fibers and the async framework](#fibers-and-the-async-framework)

## Promise resolution tracking

[*See also: related section from the article on Debugging Instrumentation*](./debugging.md#promise-resolution-tracker)

The default handlers in the main [`Promise`](../Promise.php) object implementation manage its inclusion to the [`PromiseResolutionTracker`](../Promise/PromiseResolutionTracker.php) static object. This module keeps track of all promises and ensures that they are resolved or rejected before the application exits.

A custom promise object can be made compatible with promise resolution tracking by implementing the [`IPromiseResolutionTrackerSupport`](../Promise/IPromiseResolutionTrackerSupport.php) interface.

## Queued promise resolution

Queued promise resolution is handled directly within the implementation of the event loop. It is an optimisation which prevents the call stack from nesting too deeply when dispatching promise callbacks, which allows the PHP runtime's garbage collector to get in and free irrelevant memory from the stack frames of earlier functions.

When a promise is resolved or rejected while the event loop is running, a queued promise resolver will be registered on the event loop. When the event loop is about to finish running as all events are fulfilled, it will dispatch all of the queued promise resolvers, resulting in the creation of further events. If any new events were created, then the event loop will re-enter itself.

It is not possible to turn off queued promise resolution without modifying the framework at the moment. With modification to the framework, it can be disabled by flipping the compile-time constant `ENABLE_DEFERRED_PROMISES` in [`Promise.php`](../Promise.php):

```php
/**
 * Enables promise resolution to be deferred to optimise garbage collection cycle
 * throughput and flatten the call stack a bit.
 */
const ENABLE_DEFERRED_PROMISES = true;
```

This option is mostly provided for testing and verification of the behaviour of the framework, which is why it is not provided as a runtime option.

Queued promise resolution is currently hardcoded to work with the canonical [`Promise`](../Promise.php) object.

## Implementing a custom promise class

Although the canonical implementation of a promise is the [`Promise`](../Promise.php) object, it is actually possible to implement a completely custom promise-like object. Of course, if you'd like the promise to be able to participate in standard promise-handling code, then you want to implement the interface [`IPromise`](../IPromise.php) on this object.

Since this is just an interface, your promise class does not necessarily have to behave the same way as canonical promises do. For example, you can implement a stub promise that always wraps a successful result without the ability to be rejected:

<details open>

<summary>See example</summary>

```php
/**
 * @template T
 */
final class PresucceededPromise/*<T>*/ implements IPromise
{
    public int $status = PromiseStatus::SUCCESS;
    public Throwable $reason; // Intentionally left uninitialised.
    
    /**
     * @param T $result
     */
    public function __construct(public mixed $result)
    {
    }
    
    #[Override]
    public function then(callable $cb): static
    {
        $cb($this->result);
        return $this;
    }
    
    #[Override]
    public function catch(callable $cb): static
    {
        return $this;
    }
    
    #[Override]
    public function resolve(mixed $data = null): void
    {
    }
    
    #[Override]
    public function reject($e): void
    {
    }
}
```

</details>

This approach is not recommended, and most use cases would benefit more from extending the canonical [`Promise`](../Promise.php) class instead. Inheriting from [`Promise`](../Promise.php) allows your custom promise type to be used in lieu of a `Promise` type, as opposed to an `IPromise` type. There is, however, more overhead to the canonical [`Promise`](../Promise.php) type, as it implements several other interfaces.

<details>

<summary>See above example inherited from Promise</summary>

```php
/**
 * @template T
 */
final class PresucceededPromise/*<T>*/ extends Promise
{
    /**
     * @param T $data
     */
    public function __construct(mixed $result)
    {
        // Call the parent constructor in order to initialise the
        // promise stack traces, tracking cookie, and other similar
        // things.
        parent::__construct();
        
        // Instantly resolve the promise with the desired result.
        parent::resolve($result);
    }
    
    #[Override]
    public function then(callable $cb): static
    {
        $cb($this->result);
        return $this;
    }
    
    #[Override]
    public function catch(callable $cb): static
    {
        return $this;
    }
    
    #[Override]
    public function resolve(mixed $data = null): void
    {
    }
    
    #[Override]
    public function reject($e): void
    {
    }
}
```

</details>

## Promise behaviour versions

The [`Promise`](../Promise.php) object currently reserves the ability to provide versioned behaviour, allowing for backwards-compatible breaking changes to the engine. This is currently not used, but there was one experiment under which it was used (premature returning from promise initialisation callbacks upon resolution/rejection, removed in the following commit):

https://github.com/Rehike/Rehike/commit/ea4a69f979d1031e2ec8f740b5c3a8c24ea177f1

## Fibers and the async framework

Fibers <a title="Link to PHP documentation for Fibers" href="https://www.php.net/manual/en/language.fibers.php"><sup>PHP</sup></a> are a PHP language feature introduced in PHP 8.1 which provide an alternative approach for interruptable functions. Unlike generators, they can be paused and resumed within the middle of the execution of a callee, so they are a bit more versatile.

The async framework has no problem working with fibers in user code. [Rehike NetworkCore actually uses fibers in the event internally when making network requests via cURL on PHP 8.1+.](/modules/Rehike/Network/Handler/Curl/EventLoopRunner.php) Furthermore, it is not out of the question that events themselves could be driven by fibers, apart from backwards incompatibility with current generator-driven events.

As such, you can always safely use fibers *within* an event or promise, so long as you don't plan on supporting PHP 8.0. It is also possible to wrap the event loop itself in a fiber, as long as all events are still fulfilled by the end of the script (be careful trying that).

The reason why the event system was not designed leveraging fibers from the beginning is quite simple: the framework was originally designed for PHP 7 (even though Rehike 0.7, which introduced the framework into Rehike, dropped support for PHP 7) and continues to support PHP 8.0.