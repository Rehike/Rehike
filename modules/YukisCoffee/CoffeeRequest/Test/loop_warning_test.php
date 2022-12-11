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

class PlaceholderEvent extends Event {
    public function onRun(): Generator/*<void>*/
    {
        yield;

        for ($i = 0; $i < 2; $i++) Loop::pause();

        yield;

        sayHello();
        $this->fulfill();
    }
}

Loop::addEvent(new PlaceholderEvent());

Loop::addEvent(new PlaceholderEvent());
Loop::addEvent(new PlaceholderEvent());
Loop::addEvent(new PlaceholderEvent());

Loop::run();

echo "hi\n";

//Loop::continue();

echo "Manual continuing of the loop\n";

//Loop::continue();

echo "\nDone!";