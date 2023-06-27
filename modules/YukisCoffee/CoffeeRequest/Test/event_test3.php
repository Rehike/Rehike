<?php
require "autoloader.php";
// Test events and promises

use YukisCoffee\CoffeeRequest\Event;
use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Deferred;
use YukisCoffee\CoffeeRequest\Loop;
use YukisCoffee\CoffeeRequest\Test\{
    HelloWorldEvent,
    TestEvent
};

class MyPromiser
{
    use Deferred/*<string>*/ { getPromise as public; }

    private Event $e;

    public function __construct()
    {
        $this->initPromise();
        $this->castEvent();
    }
    
    public function castEvent(): void
    {
        $this->e = (new class extends Event {
            private Promise $p;

            public function onRun(): Generator/*<void>*/
            {
                $endTime = time() + 5; // seconds

                while (time() < $endTime)
                {
                    yield;
                }

                $this->p->resolve("Promise finished.");
                $this->fulfill();
            }

            public function bindPromise(Promise $p): Event
            {
                $this->p = $p;

                return $this;
            }
        })->bindPromise($this->getPromise());

        Loop::addEvent($this->e);
    }
}

$promiser = new MyPromiser();

$promise = $promiser->getPromise();

$promise->then(function($result): void {
    echo "\n\n" . $result;
});

Loop::addEvent(new HelloWorldEvent());

Loop::run();