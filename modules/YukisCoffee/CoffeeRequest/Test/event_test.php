<?php
require "autoloader.php";
// Test events

use YukisCoffee\CoffeeRequest\Loop;
use YukisCoffee\CoffeeRequest\Test\{
    HelloWorldEvent,
    TestEvent
};

Loop::addEvent(new TestEvent());
Loop::addEvent(new HelloWorldEvent());

echo "Loop is starting\n\n";

Loop::run();

echo "\n\nLoop is finished";