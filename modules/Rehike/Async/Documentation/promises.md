# Promises

**Promises** are the main top-level abstraction provided by the async framework. Promises are named such because they *promise* resolution or rejection in the future. As such, they are objects that work with future data by registering callbacks.

The promise API provided by the async framework is modelled after that of JavaScript.

Every promise can have callbacks registered on it that listens for one or more of its two basic events. The basic events fired by a promise are its resolution event (listened for with a `then` callback) and its rejection event (listened for with a `catch` callback). As such, promises support advanced control flow which allows for easy error handling.

This article provides a general overview of what promises are and how all developers are expected to understand them. For engine implementation details, [please see "Advanced Promises" for more information.](promises_advanced.md)

## Table of contents

1. [Promise status](#promise-status)
2. [Generic types](#generic-types)
3. [Working with promises](#working-with-promises)
   1. [Cleaner code with async functions](#cleaner-code-with-async-functions)
   2. [Awaiting multiple promises at once](#awaiting-multiple-promises-at-once)
4. [Creating promises](#creating-promises)
   1. [The only thing async functions cannot do](#the-only-thing-async-functions-cannot-do)

## Promise status

Each promise object has a status, represented with the [`PromiseStatus`](../Promise/PromiseStatus.php) enum. The status of a promise may *only* be set once, from its initial status of `PENDING` to either of the two statuses recognising its dispatched event. The following promise statuses are recognised:

| **Name** | **Description** |
|----------|-----------------|
| `PENDING` | The promise is currently pending resolution. This is the default status. A pending promise must be waited for asynchronously. |
| `RESOLVED` | The promise was successfully resolved, and its data may now be safely accessed, even synchronously. Upon resolution, the promise will call its `then` handler if it is present. |
| `REJECTED` | The promise was rejected, and a reason is stated within its object. The reason may be synchronously accessed on the object, or accessed synchronously from a `catch` handler registered on the promise. |

## The `Promise` API

Almost all promises that you'll be working with are `Promise` objects. All `Promise` objects are guaranteed to have the following read-only, public properties, as defined by the `IPromise` interface:

| **Property name** | **Type** | **Description** |
|-------------------|----------|-----------------|
| `$status` | [`PromiseStatus`](../Promise/PromiseStatus.php) | Represents the current status of a promise.
| `$result` | `mixed`; *`T`* <sup title="Promises are a container format using generic types, where &quot;T&quot; is a placeholder for the wrapped type name. This allows making stricter type assertions than the `mixed` type alone allows.">(?)</sup> | The promise result. This should only be accessed if the promise is resolved. Otherwise, it may be null *or* unset.
| `$reason` | `Throwable` | The promise failure reason. This should only be accessed if the promise is rejected. Otherwise, it may be null *or* unset.

Along with this, all promise objects must implement the following methods, as defined by the `IPromise` interface:

<details open>

<summary>See methods</summary>

```php
/**
 * Register a function to be called upon a promise's
 * resolution.
 * 
 * @param callable<mixed>
 * @return IPromise<T>
 */
public function then(callable/*<mixed>*/ $cb): IPromise/*<T>*/;

/**
 * Register a function to be called upon an error occurring
 * during a promise's resolution.
 * 
 * @param callable<Throwable> $cb
 * @return IPromise<T>
 */
public function catch(callable/*<Throwable>*/ $cb): IPromise/*<T>*/;

/**
 * Resolve a promise.
 * 
 * @param T $data
 */
public function resolve(mixed $data = null): void;

/**
 * Reject a Promise (error).
 * 
 * @internal
 * @param 
 */
public function reject(string|Throwable $e): void;
```

</details>

Additionally, the canonical `Promise` object exposes the following API (as required by the [`IPromiseWithStackTrace`](../Promise/IPromiseWithStackTrace.php) interface):

| **Property name** | **Type** | **Description** |
|-------------------|----------|-----------------|
| `$creationTrace` | [`PromiseStackTrace`](../Debugging/PromiseStackTrace.php) | The stack trace of the promise at its creation. |
| `$latestTrace` | [`PromiseStackTrace`](../Debugging/PromiseStackTrace.php) | The stack trace of the promise as of the last update. |

<details open>

<summary>See methods</summary>

```php
/**
 * Sets the version of the Promise behavior.
 * 
 * Different versions can influence how which behavior works. The promise
 * version does not matter for anonymous Promises, which always use the
 * latest version, but they might matter for manual Promises (i.e. in a
 * Deferred class).
 */
public function setVersion(int $version): void;

/**
 * API function to await an array of Promises, and then
 * return a new Promise with its values.
 * 
 * @param IPromise<mixed>[]|...IPromise $promises
 * @return Promise<mixed[]>
 */
public static function all(...$promises): Promise/*<array>*/;

/**
 * Check if the script is currently in a Promise callback.
 */
public static function isCurrentlyInPromise(): bool;

/**
 * Gets the tracking cookie of the promise.
 */
public function getTrackingCookie(): TrackingCookie;

/**
 * Gets whether or not an exception shall be thrown if the promise is not
 * resolved by the end of the script.
 */
public function throwsOnUnresolved(): bool
```

</details>

## Generic types

Promises try to be a well-typed container format for delivery, meaning that they should be able to transport type information without that being lost in delivery. As such, the promise API mimicks generic types in many places. Unfortunately, generic types are not a real PHP feature, so there's no runtime benefit to doing this, but some IDEs are compatible with generic type conventions, and pretending that they're there still helps make your code easier to understand.

If you don't know what that means, here's a basic example: if you have two promises, one that's expected to resolve with a `Response`, and another which is expected to resolve with a `string`, then you can annotate this expectation using a PHPDoc comment like such:

```php
/**
 * @return Promise<Response>
 */
public function getResponse(): Promise/*<Response>*/;

/**
 * @return Promise<string>
 */
public function getString(): Promise/*<string>*/;
```

<sup>In Rehike, we have the additional habit to put a fake generic comment at the end of function return type definitions.</sup>

With these generic type annotations, it is now possible for the user of a function which returns a promise to know what type to expect to get out of it. Of course, the type can also be complex, such as a union like `string|int` or `mixed`.

## Working with promises

The most common case for working with the async framework is that you need to deal with promises returned from some other part of the codebase. One worthwhile consideration to make is that **promises are poisonous**, meaning that once you have one function returning a promise, anything relying on its result (other than a function which returns `void` and does not need to be waited for) will also have to be made into a promise.

The execution of promises almost always requires the event loop to run, so it is important that you add a provision for running the event loop somewhere where all your code can return to (i.e. the end of the program):

```php
use Rehike\Async\EventLoop;

EventLoop::run();
```

A real-world example of working with promises is working with network requests. For example, if you want to request the `www.google.com` homepage using Rehike's NetworkCore module:

```php
private function requestGoogleHomepage(): void
{
    NetworkCore::request("https://www.google.com")
        ->then(function(Response $response)
        {
            $this->handleResponse($response->getText());
        })
        ->catch(function(Throwable $e)
        {
            $this->logError($e);
        });
}

public function main(): void
{
    $this->requestGoogleHomepage();
    
    // Let's say we have pre-existing code which already does this following
    // action. Oh no! What if finishing the program's startup depends on
    // state from the promise? Then we literally cannot access it because
    // unfulfilled promises represent future data that we haven't gotten yet.
    $this->finishProgramStartup();
    
    // Always remember to include this at the end of your program so
    // the promise event loop can execute:
    EventLoop::run();
}
```

Notice that this code is ultimately very callback-oriented. The function doesn't return anything; it just gives the value to a function that it promises to call later upon its resolution or rejection. This can make it difficult to integrate into existing code.

One thing that you can do to alleviate these pains is return the inner promise. This is why promises are poisonous, since you would need to restructure code that needs to request this to also register future event callbacks with the promise, all the way down to the point of origin. However, if you're willing to restructure it, then it becomes workable.

The following example *returns a promise* instead of void, allowing its result to be accessed later:

```php
/**
 * @return Promise<Response>
 */
private function requestGoogleHomepage(): Promise
{
    return NetworkCore::request("https://www.google.com");
}

public function main(): void
{
    $this->requestGoogleHomepage()
        ->then(function(Response $response)
        {
            $this->handleResponse($response->getText());
            $this->finishProgramStartup();
        })
        ->catch($this->handleFailure(...));
    
    // Always remember to include this at the end of your program so
    // the promise event loop can execute:
    EventLoop::run();
}
```

In this case, by returning the `Promise` from our function, we pass over the callback controller for a different function to handle. This is workable, but it can still require restructuring your pre-existing code, and it can get quite ugly very fast.

### Cleaner code with async functions

*[See also: dedicated article on Async Functions](./async_functions.md)*

A very natural way to work with promises is to use the async function wrapper. This is provided by the framework under the `Concurrency` static class.

```php
use Rehike\Async\Concurrency;

// In this case, you call it with Concurrency::async(function() { ... });
```

Rehike itself also has a shorter alias provided as a function:

```php
use function Rehike\Async\async;

// In this case, you just call async(function() { ... });
```

Async functions mimick the structure of regular procedural functions, but use promises underneath. This allows you to trivially restructure existing code to be promise-oriented. All you need to do is wrap the function in a `return Concurrency::async(function() { ... })` wrapper, and you're good to go. In async functions, you can use standard control flow, including try/catch blocks, for working with promises. For example:

```php
/**
 * @return Promise<Response>
 */
private function requestGoogleHomepage(): Promise
{
    return NetworkCore::request("https://www.google.com");
}

public function main(): void
{
    Concurrency::async(function()
    {
        try
        {
            // In async functions, the "yield" keyword is used to "unwrap"
            // the result of a promise. If the promise was rejected, then
            // yielding here will throw the exception associated with its
            // rejection. This is similar to native async functions in other
            // languages which use the "await" keyword.
            $response = yield $this->requestGoogleHomepage();
            
            $this->handleResponse($response->getText());
            $this->finishProgramStartup();
        }
        catch (Throwable $e)
        {
            $this->handleFailure($e);
        }
    });
    
    // Always remember to include this at the end of your program so
    // the promise event loop can execute:
    EventLoop::run();
}
```

### Awaiting multiple promises at once

If you have multiple promises that you need to await at once, then you can use the `Promise::all()` method for that, passing a series of promises as an array or multiple arguments. The function will return an array of the promise results in the order that the promises were passed upon upon success. Upon failure, a [`PromiseAllException`](../Exception/PromiseAllException.php) is thrown.

For example:

```php
$watchRequest = Network::innertubeRequest("watch", ...);
$playerRequest = Network::innertubeRequest("player", ...);

[$watchResponse, $playerResponse] = yield Promise::all($watchRequest, $playerRequest);
```

Associative arrays with string keys can also be passed to `Promise::all()` and the result array will maintain the indices of the input array. As such, the above example can be also be written as:

```php
$responses = yield Promise::all([
    "watch" => Network::innertubeRequest("watch", ...),
    "player" => Network::innertubeRequest("player", ...),
]);

$watchResponse = $responses["watch"];
$playerResponse = $responses["player"];
```

### "Optional" promises

Sometimes you only really need to wait for something on a condition, but it would be a bit messy to structure your code to only use promises sometimes. Fortunately, promises can operate entirely synchronously, so you can actually substitute an asynchronous promise for a synchronous stub promise in some cases.

Consider the following:

```php
if (Config::getConfigProp("appearance.useRyd"))
{
    $rydRequest = Network::urlRequest(RYD_API_URL, ...);
}
else
{
    $rydRequest = new Promise(fn($r) => $r());
}
```

In the above example, a promise is always created. If the option to use the RYD service is enabled, then a network request to the service will be made, which is an asynchronous operation. Otherwise, a stub promise which instantly fulfills itself is made.

> [!TIP]
> `fn($r) => $r()` is shorthand for
>
> ```php
> function (callable $resolve) use (...)
> {
>     return $resolve();
> }
> ```
>
> By the way, these shorthand function expressions in PHP automatically inherit all variables from the parent scope, so you don't need to use `use ()` at all.

This pattern can be used advantageously with, for example, a `Promise::all()` call.

---

With this knowledge, you should now be able to effectively work with promises within existing codebases utilising the framework.

## Creating promises

Sometimes, it's useful to create your own promises. There are multiple different ways to create promises, such as deferred controllers, but this section of the article will focus on the main way to create promises, which is through the `new Promise(function() { ... })` constructor pattern. These are also known by their implementation name: "anonymous promises".

> [!NOTE]
> Promises can also be constructed without a construction callback, via `new Promise()`.
>
> These are backed promises intended for deferred controllers, and will otherwise not do anything. [See the article on "Deferred APIs" for more information.](./deferred.md)
>
> Promises with constructor callbacks receive automatically generated events which are put into the event loop, which makes them easy to use and hard to mess up. Backed promises require active, manual management by their controllers.

Promises with a construction callback may receive two positional callback arguments to control resolution and rejection within the callback. These arguments come in the following order:

| **Index** | **Conventional name** | **Prototype** | **Behaviour**|
|-----------|-----------------------|---------------|--------------|
| 0th | `$resolve` | `function(T $result): void` | Resolves the promise with a result variable.
| 1st | `$reject` | `function(Throwable $exception): void` | Rejects the promise with a throwable exception or error. This argument does not have to be specified if unused.

For an example, let's return to our `requestGoogleHomepage` function from earlier, but modify it to optionally read from a file for debugging purposes.

We start with this, a simple function which returns a promise from another function:

```php
/**
 * @return Promise<Response>
 */
private function requestGoogleHomepage(): Promise
{
    return NetworkCore::request("https://www.google.com");
}
```

and we'll restructure to this:

```php
/**
 * @return Promise<Response>
 */
private function requestGoogleHomepage(): Promise
{
    return new Promise(function(callable $resolve, callable $reject): void
    {
        if ($this->debug && isset($this->debugGoogleHomepageFilePath))
        {
            $fileContents = file_get_contents($this->debugGoogleHomepageFilePath);
            $resolve($fileContents);
        }
        else
        {
            NetworkCore::request("https://www.google.com")
                ->then(function(Response $response) use ($resolve): void
                {
                    $resolve($response);
                })
                ->catch(function(Throwable $e) use ($reject): void
                {
                    $reject($e);
                });
        }
    });
}
```

The above example also demonstrates the verbosity of direct promise creation. There is an even easier alternative. As you may imagine, it's using **async functions**!! Async functions are capable of almost everything that explicit `new Promise(fn)`-style promise creation, while having one single limitation.

### The only thing async functions cannot do:

Yield the *event* backing the promise. Since the `yield` keyword is overloaded for async functions to await the resolution of a promise, there is no way to temporarily exit the body of an async function and resume it later without having a promise to wait for. On the other hand, the `Promise` constructor accepts generator functions as callbacks, and thus can freely be exited from and resumed.

This is a rather rare use case of promises in the async framework, but you may encounter it in situations where you are trying to optimise the workload of a function.