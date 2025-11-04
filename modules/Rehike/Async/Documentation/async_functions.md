# Async Functions

**Async functions** are an easy mechanism for writing asynchronous code. They maintain the structure of standard, synchronous code, rendering them easy vessels to understand and migrate existing code to an asynchronous format.

## Overview

Async functions are a wrapper for coroutines<a href="https://en.wikipedia.org/wiki/Coroutine"><sup><abbr title="Link to the Wikipedia article for Coroutines">WP</abbr></sup></a> driven by [promises](./promises.md), based on generators<a title="Link to PHP documentation for Generators" href="https://www.php.net/manual/en/language.generators.overview.php"><sup>PHP</sup></a>. Within an async function, the `yield` keyword may be used on a promise object to await the promise, which means splitting the rest of the routine on the basis of the promise's resolution or rejection. The results of resolved promises are the result of a yielded expression. If the promise is rejected, its reason will be thrown.

Async functions bear a resemblance to the first-class language feature seen in JavaScript<a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/async_function"><sup><abbr title="Link to the Mozilla Developer Network article on async functions in JavaScript">MDN</abbr></sup></a> and C#<a href="https://learn.microsoft.com/en-us/dotnet/csharp/language-reference/keywords/async"><sup><abbr title="Link to the Microsoft Learn article on async functions in C#">MS</abbr></sup></a>, but PHP lacks any such feature natively. As a result, async functions must be emulated using generators, which provide similar functionality.

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

## Example

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

## The only thing async functions cannot do:

Yield the *event* backing the promise. Since the `yield` keyword is overloaded for async functions to await the resolution of a promise, there is no way to temporarily exit the body of an async function and resume it later without having a promise to wait for. On the other hand, the `Promise` constructor accepts generator functions as callbacks, and thus can freely be exited from and resumed.

This is a rather rare use case of promises in the async framework, but you may encounter it in situations where you are trying to optimise the workload of a function.