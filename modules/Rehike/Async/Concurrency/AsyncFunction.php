<?php
namespace Rehike\Async\Concurrency;

use Generator, Exception;
use Rehike\Async\Promise;

/**
 * Represents an async function in execution.
 * 
 * Async functions work in Rehike by using generator functions that yield
 * promises within them. This class implements the logic necessary to
 * capture the Promise and send back the value of the Promise. 
 * 
 * @internal
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class AsyncFunction
{
    /**
     * The generator is the backend of the whole thing.
     * 
     * Generators are a perfect to emulate async functions since their
     * yield functionality is pretty much identical to await.
     * 
     * Most notably, you can capture the result of a yield expression, use
     * it, and then replace it with a completely different variable.
     */
    private Generator $generator;

    /**
     * A Promise for the completion of the async function.
     * 
     * This enables API compatibility with Promise-based code, i.e. you
     * can `myAsyncFunction()->then("handleAsyncResult")`. When the async
     * function returns or throws, this Promise will be resolved or rejected
     * respectively.
     */
    private Promise $ownPromise;

    public static function __initStatic()
    {
        \YukisCoffee\CoffeeRequest\Debugging\PromiseStackTrace::registerSkippedFile(__FILE__);
    }

    public function __construct(Generator $g)
    {
        $this->generator = $g;
        $this->ownPromise = new Promise();

        // PATCH (kirasicecreamm): Make Promises use version 0 in order to
        // prevent some weird generator error. I'm currently unable to figure
        // out why this happens.
        $this->ownPromise->setVersion(0);
    }

    /**
     * Get a Promise representing the state of the async function.
     * 
     * This will resolve or reject accordingly with the value returned
     * or thrown by the async function.
     */
    public function getPromise(): Promise
    {
        return $this->ownPromise;
    }

    /**
     * Run the async function for one iteration.
     * 
     * This progresses the internal Generator and captures the value held
     * within it as long as the Generator has not yet returned.
     * 
     * If the Generator has returned, then it will resolve this function's
     * Promise and message the return status to all internal listeners.
     * Likewise for rejection.
     */
    public function run(): void
    {
        // A valid generator is one that has not returned.
        if ($this->generator->valid())
        {
            try
            {
                /*
                 * Capture the value of the generator *and then progress
                 * it*.
                 * 
                 * Those unfamiliar with PHP's generator implementation may
                 * be a little confused with this behaviour. Whenever you
                 * try to get the current value of the generator, it will
                 * return that value and then run the generator again.
                 * 
                 * That's also why this function never explicitly calls
                 * Generator::next().
                 */
                $value = $this->generator->current();
            }
            catch (Exception $e)
            {
                // Capture the exception thrown by the Generator and carry
                // it over to the internal Promise.
                $this->ownPromise->reject($e);
            }
        }
        else // has returned
        {
            $result = $this->generator->getReturn();
            $this->ownPromise->resolve($result);
            
            return;
        }

        // Obviously, an async function must take in a Promise.
        if ($value instanceof Promise)
        {
            $value->then($this->getThenHandler());
        }
        else
        {
            throw new \Exception(
                "An async function must take in a Promise."
            );
        }
    }

    /**
     * Get a then handler for the internal Promise.
     * 
     * Only one handler is ever needed, which is this closure. This relies
     * on the Promise messaging system to send the value of the current
     * Promise back to the generator and then rerun it.
     */
    protected function getThenHandler(): callable
    {
        return function (mixed $value) {
            $this->generator->send($value);
            $this->run();
        };
    }
}