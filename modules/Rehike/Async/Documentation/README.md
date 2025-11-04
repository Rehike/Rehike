# Getting Started with the Async Framework

The async framework has a lot of different components which may be hard to understand. This article will discuss the components of the async framework in a way that is hopefully easy to digest for new readers.

For advanced users, the framework documentation may be supplementary, but it is also recommended to read source code files implementing the framework for a complete understanding.

## Components of the async framework

The async framework is comprised of many different components, some of which are crucial to its structure, and some of which build useful abstractions to make your life easier when working with them. The following table concerns the most common components that you absolutely must know about, at least in part, to effectively work with the framework.

From the most surface-level, easiest-to-grasp components to the true guts of the framework:

| **Name** | **Description** |
|----------|-----------------|
| [Promise](promises.md) | Promises are the bedrock of the high-level API. They expose a basic object-oriented API that allows the user to subscribe callback functions to listen for either successful or failed responses and handle each case accordingly. Promises may be backed by events and generators, allowing them to pause execution in the middle of a function and come back to it later. |
| [Async functions](async_functions.md) | Async functions are a wrapper around promises. They provide syntactic sugar which allows you to write promise-oriented code in a matter which resembles regular procedural code. This effect is achieved using generators. |
| [Deferred APIs](deferred.md) | Deferred APIs are manual promise controllers. Most promises made by higher-level code are automatic "anonymous" promises, which have an initial function that they execute and which resolve or reject themselves. Deferred controllers externally resolve or reject a promise that they own. |
| [Events](event_loop.md#events) | Events are the core of the async framework. Events are generator-function-providing objects which are ran periodically in the event loop. Although events are the premise of higher-level APIs which have methods of safely transferring state, events themselves only have internal state, and can only be suspended or fulfilled, affecting their status in the event loop.  |
| [Event loop](event_loop.md) | The event loop runs through all active, unfulfilled events until either it is paused or there are no more remaining. It is the backbone to the async framework. In order for any of the asynchronous code to be able to run in the first place, the event loop must first be called into from synchronous code. |
| Generators <a title="Link to PHP documentation for Generators" href="https://www.php.net/manual/en/language.generators.overview.php"><sup>PHP</sup></a> | Generators are actually a feature provided by the PHP engine itself, but they are the core foundation of the framework. A generator function can pause execution in the middle of its body and resume from that very point at which it stopped at any time in the future, which allows them to be utilised to implement coroutines, which are the basic premise of the framework. |
| **[Debugging instrumentation](debugging.md)** | Documentation for the debugging instrumentation of the async framework. |

## Related components in Rehike

The async framework was originally written as part of the Rehike 0.7 (codenamed Asynchike) update in 2022 to replace the original slow-and-fragile networking stack of Rehike.

One core Rehike component was written alongside the async framework:

### [NetworkCore (formerly known as CoffeeRequest)](/modules/Rehike/Network/)

Well, that's a bit of a lie. Under the original design by Taniko Yamamoto, the async framework was actually a part of CoffeeRequest, which was her own networking library used in Rehike as third-party code. They were later split into separate components when CoffeeRequest was forked into Rehike NetworkCore.

## Maintainers of the async framework

- [Taniko Yamamoto](//github.com/YukisCoffee) (link to an account archive) - No longer involved with the Rehike project and generally inactive, but she is the original author of the majority of the async framework.
  - The original project, as previously mentioned, was known as [CoffeeRequest](//github.com/YukisCoffee/CoffeeRequest), and was part of Taniko's personal library. In lieu of her maintenance, the project has been forked into the current incarnation of the async framework.
- [Isabella Lulamoon](//github.com/kawapure) (email: [kawapure@gmail.com](mailto:kawapure+rehike@gmail.com)) - Current maintainer, Rehike project lead.