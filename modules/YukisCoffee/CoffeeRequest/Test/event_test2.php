<?php
require "autoloader.php";
// Test events

use YukisCoffee\CoffeeRequest\Event;
use YukisCoffee\CoffeeRequest\Loop;
use YukisCoffee\CoffeeRequest\Test\{
    HelloWorldEvent,
    TestEvent
};

function sayHello()
{
    echo "\nHello world!\n";
}

Loop::addEvent(new class extends Event {
    public function onRun(): Generator/*<void>*/
    {
        yield;

        for ($i = 0; $i < 2; $i++) Loop::pause();

        yield;

        sayHello();
        $this->fulfill();
    }
});

Loop::run();

echo "hi\n";

Loop::continue();

echo "Manual continuing of the loop\n";

Loop::continue();

echo "\nDone!";