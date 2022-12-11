<?php
namespace YukisCoffee\CoffeeRequest\Test;

use YukisCoffee\CoffeeRequest\Event;

use Generator;

class TestEvent extends Event
{
    public function onRun(): Generator/*<void>*/
    {
        for ($i = 0; $i < 15; $i++)
        {
            echo "$i ";
            yield;
        }

        $this->fulfill();
    }
}