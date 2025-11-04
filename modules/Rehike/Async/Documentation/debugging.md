# Debugging Instrumentation of the Async Framework

This article discusses the debugging instrumentation of the async framework.

## Table of contents

1. [Promise stack traces](#promise-stack-traces)
2. [Promise resolution tracker](#promise-resolution-tracker)
3. [Tracking cookies](#tracking-cookies)
4. [Tracing system](#tracing-system)
   1. [Component IDs](#component-ids)
   2. [Trace event IDs](#trace-event-ids)

## Promise stack traces

Promise stack traces were one of the original debugging instruments used within the framework. Each [`Promise`](../Promise.php) object has two stack traces: a static one for the lifetime of the promise set up when the promise was originally constructed, and a dynamic one which changes every time the promise is mutated.

> ![NOTE]
> [Tracking cookies](#tracking-cookies) on promises also contain a separate copy of the initial stack frame of the promise in order to fulfill the API contract of tracking cookies.

Custom promises do not have to provide stack traces, but they can by implementing the [`IPromiseWithStackTrace`](../Promise/IPromiseWithStackTrace.php) interface. Promise stack traces are a prerequisite for some other debugging instruments, such as the promise resolution tracker.

## Promise resolution tracker

The promise resolution tracker was designed as a module which, if enabled, did exactly what it said on the tin: threw an exception ([`UnhandledPromiseException`](../Exception/UnhandledPromiseException.php)) at the end of the script's runtime if any tracked promise is left pending. This system made up the other of the two original debugging instruments of the framework.

Its use in Rehike has since been mostly reduced to logging unhandled promises to the debug log instead of crashing the script, since that's actually easier to debug.

Below is a description of the [`PromiseResolutionTracker`](../Promise/PromiseResolutionTracker.php) public API:

<table>
    <tr>
        <td><b>Method</b></td>
        <td><b>Description</b></td>
    </tr>
    <tr>
<td>
        
```php
static function disable(): void
```
  
</td>
<td>

Disables the error handling of the promise resolution tracker.

</td>
    </tr>
    <tr>
<td>
        
```php
static function enable(): void
```
  
</td>
<td>

Enables the error handling of the promise resolution tracker.

</td>
    </tr>
    <tr>
<td>
        
```php
static function logPendingPromisesNow(): void
```
  
</td>
<td>

Logs all pending promises to the Rehike [`DebugLogger`](../../Logging/DebugLogger.php). This does not require the promise resolution tracker's error handling to be enabled.

</td>
    </tr>
    <tr>
<td>
        
```php
static function registerPendingPromise(IPromise $promise): void
```
  
</td>
<td>

Registers a promise object as pending.

</td>
    </tr>
    <tr>
<td>
        
```php
static function unregisterPendingPromise(IPromise $promise): void
```
  
</td>
<td>

Removes a promise object from the resolution tracker because it was handled or is being removed.<sup title="For that matter, promises in the resolution tracker are kept in memory until they are resolved, so unused promises cannot be destructed. This could be resolved in the future by rewriting the code to use weak references instead.">(yap)</sup>

</td>
    </tr>
</table>

A custom promise object can be made compatible with promise resolution tracking by implementing the [`IPromiseResolutionTrackerSupport`](../Promise/IPromiseResolutionTrackerSupport.php) AND [`IPromiseWithStackTrace`](../Promise/IPromiseWithStackTrace.php) interfaces. Promises are typically added to the promise resolution tracker during their creation (i.e. their constructors) via the function `PromiseResolutionTracker::registerPendingPromise`. When a promise is resolved or rejected, then it is no longer pending, and must be removed from the promise resolution tracker. This is done via the function `PromiseResolutionTracker::unregisterPendingPromise`.

The promise resolution tracker's error handling is enabled by default. It can be disabled via calling the static method `PromiseResolutionTracker::disable()`. This present behaviour may change at any time. Please note that the logging mechanism is always active, with no way for it to be disabled at the moment.

> ![NOTE]
> The reason why the methods are simply called `enable()` and `disable()` is because the original design of the `PromiseResolutionTracker` class was solely to facilitate this error handling approach. The logging mechanism was added several years after the case in order to make debugging Rehike a bit easier.

<details>
<summary>Isabella's Opinion</summary>

I think the error handling part was a mistake, and it's usually better to have it off. The reason is that most errors will be obscured by it, so it makes things a bit harder to debug. A lot of uncaught exceptions/errors can get overshadowed by the significantly-more-meaningless `UnhandledPromiseException`, with an unrelated, usually-empty stack trace in most cases since it's created when the PHP runtime is firing off all destructors for static object instances.

In Rehike, the error handling is off by default just because it makes it so much harder to debug.

</details>

## Tracking cookies

Tracking cookies are objects which track the creation context and global creation count of a class, allowing for easy unique identification in debugging output. Every tracking cookie is unique for the application session. This should not be confused with telemetry or browser cookies; it is purely local debugging state that exists on the server for the duration of the request.

Below is a description of the [`TrackingCookie`](../Debugging/TrackingCookie.php) public API:

<table>
    <tr>
        <td><b>Method</b></td>
        <td><b>Description</b></td>
    </tr>
    <tr>
<td>
        
```php
function __construct(string $category)
```
  
</td>
<td>

Creates a tracking cookie. It is conventional to use `__CLASS__` for the categories of tracked classes, `__METHOD__` for tracked methods, etc.

</td>
    </tr>
    <tr>
<td>
        
```php
function __toString(): string
```
  
</td>
<td>

Serialises the tracking cookie into a random-seeming unique string that can be searched in a long list of debug object prints or such.

</td>
    </tr>
    <tr>
<td>
        
```php
function getCreationTrace(): string
```
  
</td>
<td>

Gets the creation stack trace of the tracking cookie as a string. This can be used to determine the origin or identity of a particular object during runtime.

</td>
    </tr>
</table>

Objects providing tracking cookies must implement the [`IObjectWithTrackingCookie`](../Debugging/IObjectWithTrackingCookie.php) interface.

The use of tracking cookies conventionally involves setting up the tracking cookie at the construction of the tracked object. The `$category` parameter of the [`TrackingCookie`](../Debugging/TrackingCookie.php) constructor should be identical under for all objects of a given class; it is conventional to use the `__CLASS__` constant to get the name of the implementing class.

> [!TIP]
> In non-final classes, ensure the existence of the tracking cookie in common methods or all default method implementations. Tracking cookies have stack traces which can be used to identify the origin of a particular object instance, but the further away from the constructor this gets, the less reliable this information may get.
>
> It's always good to reinforce the reliability of legacy or unknowledgable consumers.

Every new tracking cookie object under a specific category has a new unique ID. For ease of implementation, this is currently just an incrementing integer beginning with 0.

Tracking cookies serialise into an MD5 checksum of the category and the unique ID of the instance. This serialised string provides the uniqueness necessary to quickly identify a particular object in a long list of debug logs.

## Tracing system

The tracing system, [implemented in the `Tracing` module](../Debugging/Tracing.php) is a system for internal logging of the behaviour of the async framework. It tracks different event types via a unique [`TraceEventId`](../Debugging/TraceEventId.php) for that type, and allows an object to be specified in a log, such as a tracking cookie.

This system was developed to improve the developer experience of debugging the async framework.

### Component IDs

The tracing system tracks several different common components of the async framework. Currently, the following components are tracked:

| **Constant** | **ID** | Description |
|--------------|--------|-------------|
| `CID_EVENT`  | 0 | Tracks events of the event loop ([`Event`](../EventLoop/Event.php)). |
| `CID_EVENTLOOP`  | 1 | Tracks the event loop itself ([`EventLoop`](../EventLoop/EventLoop.php)). |
| `CID_PROMISE`  | 2 | Tracks engine-implemented promises ([`Promise`](../Promise.php)). |
| `CID_QUEUEDPROMISE`  | 3 | Tracks the queued promise resolver ([`QueuedPromiseResolver`](../Promise/QueuedPromiseResolver.php)). |
| `CID_ASYNCFUNC`  | 4 | Tracks async functions ([`AsyncFunction`](../Concurrency/AsyncFunction.php)). |

New components IDs may be added to track new components in the future. They will be added incrementally. **The ID of a component will never change after its addition.**

### Trace event IDs

Trace event IDs are 32-bit values which have an internal structure to them, which is as follows:

<table>

<!-- -->
<tr>

<td><!-- for row name -->
<b>Bit range</b>
</td>
<td><!-- for Severity -->
<b>31</b>
</td>
<td><!-- for Component -->
<b>30~16</b>
</td>
<td><!-- for Status -->
<b>15~0</b>
</td>
    
</tr>

<!-- -->
<tr>

<td><!-- for row name -->
<b>Field</b>
</td>
<td><!-- for Severity -->
Severity
</td>
<td><!-- for Component -->
Component
</td>
<td><!-- for Status -->
Status
</td>
    
</tr>

<!-- -->
<tr>

<td valign=top><!-- for row name -->
<b>Description</b>
</td>
<td valign=top><!-- for Severity -->

**A boolean failure value.** A value of `TE_SUCCESS` (0) indicates a successful event. A value of `TE_FAIL` (1) indicates a failed event.

</td>
<td valign=top><!-- for Component -->

**The ID of the component for the event.** See ["Component IDs"](#component-ids) for a list of component IDs.

There are 14 bits available to this part, allowing for **16,384** distinct component IDs.

</td>
<td valign=top><!-- for Status -->

**The status of the event.** This is a unique value to a particular component that can identify various different event types.

The only conventions are that components that align with instantiated objects tend to reserve the 0th and 1st statuses for "Create" and "Destroy", and that the IDs should never shift.

For example, here are all statuses for the event component:

- `EventCreate`: 0
- `EventDestroy`: 1
- `EventRun`: 2
- `EventFulfill`: 3

There are 15 bits available to this part, allowing for **32,768** distinct statuses for a single component.

</td>
    
</tr>

</table>

All constants, component IDs, and full trace event IDs are defined in the [`TraceEventId`](../Debugging/TraceEventId.php) static class. Additionally, that class provides useful functions for parsing the structures of trace event IDs:

<table>
    <tr>
        <td><b>Method</b></td>
        <td><b>Description</b></td>
    </tr>
    <tr>
<td>
        
```php
static function getStatusAlone(int $eid): int
```
  
</td>
<td>

Gets the status of an event ID. This will always be one of:
- `TE_SUCCESS` (0)
- `TE_FAIL` (1)

</td>
    </tr>
    <tr>
<td>
        
```php
static function getComponent(int $eid): int
```
  
</td>
<td>

Gets the component ID of an event ID.

</td>
    </tr>
    <tr>
<td>
        
```php
static function success(int $eid): bool
```
  
</td>
<td>

Checks if the status of an event ID is `TE_SUCCESS`.

</td>
    </tr>
    <tr>
<td>
        
```php
static function failed(int $eid): bool
```
  
</td>
<td>

Checks if the status of an event ID is `TE_FAIL`.

</td>
    </tr>
    <tr>
<td>
        
```php
static function makeSuccessful(int $eid): int
```
  
</td>
<td>

Makes a copy of the event ID, and set its status to successful.

</td>
    </tr>
    <tr>
<td>
        
```php
static function makeFailed(int $eid): int
```
  
</td>
<td>

Makes a copy of the event ID, and set its status to failed.

</td>
</table>