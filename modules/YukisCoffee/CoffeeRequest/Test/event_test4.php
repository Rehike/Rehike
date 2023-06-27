<?php
require "autoloader.php";
// Anonymous promises

use YukisCoffee\CoffeeRequest\Event;
use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Deferred;
use YukisCoffee\CoffeeRequest\Loop;
use YukisCoffee\CoffeeRequest\Test\{
    HelloWorldEvent,
    TestEvent
};

function testPromise(): Promise/*<string>*/
{
    return new Promise/*<string>*/(function ($resolve, $reject): Generator/*<void>*/ {
        yield;

        $reject("Hello world!");

        $resolve("This will never be sent.");
    });
}

/*
 * Non-generator anon Promises are handled synchronously. This is because
 * it's hard to create a Generator from a standard anonymous function.
 * 
 * I could just limit the programmer ability to only Generators, but this
 * is more versatile and may have some use.
 */
function syncPromise(): Promise/*<string>*/
{
    return new Promise/*<string>*/(function ($resolve, $reject): void {
        $resolve(
            "This should echo first, since it runs synchronously " .
            "before the event loop even starts."
        );
    });
}

function lastPromise(): Promise/*<string>*/
{
    return new Promise/*<string>*/(function ($resolve, $reject): Generator/*<void>*/ {
        yield;

        $resolve("Promises are pretty cool.");
    });
}

testPromise()
    ->then(function (string $result): void {
        // This should never run because the Promise is rejected
        // before it is resolved.
        echo "\n\n" . $result;
    })
    ->catch(function (Exception $e): void {
        // This, on the other hand, should run and echo the Exception's
        // message.
        echo "\n\n" . $e->getMessage();
    })
;

// This should run first
syncPromise()->then(function (string $result): void {
    echo "\n\n" . $result;
});

// This should still run last since the event was registered last.
lastPromise()->then(function (string $result): void {
    echo "\n\n" . $result;
});

Loop::run();