# The Event Loop

**The event loop** is the central part of the async framework, upon which all other components are built. It manages the ability to run basic asynchronous code in PHP via, as the name suggests, looping over several event objects which have related callback functions. This provides a primitive form of asynchronous operation.

The primitive execution method provided by the event loop is known as **cooperative multitasking**, and it is characterised by each component in the system signaling when to switch to the next.

## Table of contents

1. [Overview](#overview)
2. [The event loop API](#the-event-loop-api)
3. [Events](#events)
   1. [`IEvent` and `NullEvent`](#ievent-and-nullevent)
4. [Pausing the event loop](#pausing-the-event-loop)

## Overview

For most users of the framework, the most you will have to do with the event loop is enter it. Manually entering the event loop is necessary in order to run events, which are the backbone of the rest of the framework, including promises. Fortunately, that is quite simple:

```php
use Rehike\Async\EventLoop\EventLoop;

// Put this somewhere where your code will naturally fall to after all
// synchronous initialisation is complete:
EventLoop::run();
```

While the event loop is running, it will block any code after the `EventLoop::run()` call from executing. Only events will be able to run. This isn't really a problem, because a lot of code can just run within events, with little-to-no modification required.

If you are looking to use the async framework to optimise code, or to maintain the framework, then it can be useful to understand how the event loop really works. The rest of this article will detail the event loop API, design rationale, and some implementation details.

## The event loop API

Import the event loop using the following use declaration:
```php
use Rehike\Async\EventLoop\EventLoop;
```

<details>

<summary>See methods</summary>

```php
/**
 * Run the event loop.
 * 
 * Running the event loop will block further code execution, so
 * still think of this as a synchronous operation.
 * 
 * Although this call may be synchronous, what happens with the
 * events isn't, so you can implement your own asynchronous handlers
 * using the Events API if need be.
 */
public static function run(): void;

/**
 * Determine if the event loop has an event.
 */
public static function hasEvent(IEvent $e): bool;

/**
 * Add an event to the event loop.
 */
public static function addEvent(IEvent $e): void;

/**
 * Add an event to the event loop if it's not already there.
 */
public static function addEventIfNotAdded(IEvent $e): void;

/**
 * Remove an event from the event loop.
 */
public static function removeEvent(IEvent $e): void;

/**
 * Reports whether or not the event loop is currently paused.
 */
public static function isPaused(): bool;

/**
 * Check if the loop is finished running.
 * 
 * Unlike checking if the loop should continue running, this does
 * not return true if the event loop is paused.
 */
public static function isFinished(): bool;

/**
 * Pauses the event loop.
 * 
 * When the event loop is paused, code declared outside of events
 * continues to execute synchronously until the event loop is
 * manually continued.
 * 
 * Naturally, the event loop can only be paused within an event. Be
 * careful to continue the event loop afterwards. If you make a mistake
 * and the event loop is never continued, a warning will be displayed to
 * notify you of your probable mistake.
 * 
 * This is an advanced feature that has few use cases, but it is
 * supported.
 */
public static function pause(): void;

/**
 * Continues event loop execution and unpauses the loop.
 */
public static function continue($autoRun = true): void;

/**
 * Add a QueuedPromiseResolver to the queue.
 */
public static function addQueuedPromise(QueuedPromiseResolver $p): void;
```

</details>

## Events

Events are objects backing resumable functions, which are implemented in PHP via generators. Events are ran in a loop, as long as they're not suspended, until they are fulfilled.

The following event flags may be specified:

| **Name** | **Description** |
|----------|-----------------|
| `Suspended` | The event is suspended. Suspended events are not executed at all for as long as they are in a suspended state. However, unlike fulfilled events, their presence in the event loop is maintained. |
| `AllowQueuedPromisesToPass` | The event loop will exit if only events of this type exist in the event queue. <sup title="When the event loop exits, queued promise resolvers are dispatched, and those tend to spawn off new promises, which causes the event loop to re-enter itself.">(How does this work?)</sup> [Read more about queued promise resolution.](./promises_advanced.md#queued-promise-resolution) |
| `MayResetFulfillment` | Excludes the event from automatic clean up upon fulfillment, allowing the fulfillment state to be reset.<sup title="Yes, I know. Why not just use suspension to achieve this effect? Well, it's just a backwards compatibility thing stemming from the earliest versions of the framework, before event suspension existed. The semantic difference still holds a point now, but both are functionally the same.">(yap)</sup> |
| `NoRunLimit` | The event is exempt from the standard event run limit of a few myriad<sup title="The current limit in the implementation is 2^15, or 32,768 times. If you didn't know, a myriad is a factor of 10,000">(*)</sup> times. |

The [`Event`](../EventLoop/Event.php) class is an abstract class that all events inherit from. Every event must implement the `onRun()` method, which is called every time the event is ran.

> [!TIP]
> When extending the `Event` class and overriding its constructor, please be sure to call `parent::__construct()` to ensure that debugging instrumentation for the event is set up properly. This helps provide the best developer experience.

### `IEvent` and `NullEvent`

These are just implementation details for a memory optimisation, so please do not try to use them from outside of the framework. The only supported way to create an event is via extending the [`Event`](../EventLoop/Event.php) class.

Basically, fulfilled events are replaced with [`NullEvent`](../EventLoop/NullEvent.php) objects, which are specially-optimised event stubs. This allows memory associated with the event to be freed.

## Pausing the event loop

The event loop can be paused in the middle of execution, which allows temporarily interrupting the event loop to execute more synchronous code after the event loop was entered. This may help you cope with integrating event-driven code into existing code. <sup title="Isabella: Well, to be honest, that explanation is itself cope. I'm really not sure why this exists. It's existed since Niko's first incarnation of the framework, but it's never been used in Rehike, and I can't really imagine what design she was possibly trying to account for with it that wouldn't be better to just restructure. Maybe it was useful for debugging in the original design? I can't even find a case where it was used in Niko's original testing code.">(yap)</sup>

The event loop is paused using the `pause()` static method, and resumed using the `continue()` static method.