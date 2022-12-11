<?php
namespace YukisCoffee\CoffeeRequest\Test;

use YukisCoffee\CoffeeRequest\Event;
use Generator;

class HelloWorldEvent extends Event
{
    public function onRun(): Generator/*<void>*/
    {
        if (false) yield;

        echo "Hello world";

        $this->fulfill("Hello world!");
    }
}