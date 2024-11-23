<?php
namespace Rehike\Async;

use Rehike\Async\Concurrency\AsyncFunction;

use Rehike\Async\Promise;
use Rehike\Async\EventLoop\EventLoop;
use Rehike\Async\Promise\PromiseStatus;

use Rehike\Async\Debugging\PromiseStackTrace;

use Generator;

/**
 * Implements a Generator-based Promise abstraction.
 * 
 * This allows similar syntax to async functions in C# and ECMAScript by
 * using generators as a hack.
 * 
 * In fact, the hack is reminiscent of a technique used to emulate async
 * functions in ES6, when generators were available but async functions
 * were not.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Concurrency
{
    public static function __initStatic()
    {
        PromiseStackTrace::registerSkippedFile(__FILE__);
        PromiseStackTrace::registerSkippedFile(ASYNC_FUNCTION_FILE);
    }

    /**
     * Declares an async function.
     * 
     * This is an abstraction for Promises using PHP's native generator
     * functionality. It functions very similarly to async functions in
     * C# or ECMAScript 7. The main difference is it relies on an anonymous
     * function still, and uses the `yield` keyword rather than `await`.
     * 
     * For API compatiblity with standard Promises, all async functions
     * return a Promise which will resolve or reject with the internal
     * Generator it uses.
     * 
     * It's recommended to `use function Rehike\Async\async;` and use the
     * shorthand function, rather than using this class directly.
     * 
     * Usage example:
     * 
     *      function requestExample(string $url): Promise//<Response>
     *      {
     *          return Concurrency::async(function() use (&$myPromises) {
     *              echo "Making network request...";             
     * 
     *              $result = yield Network::request($url);
     *              
     *              if (200 == $result->status)
     *              {
     *                  return $result;
     *              }
     *              else
     *              {
     *                  throw new NetworkFailedExeception($result);
     *              }
     *          });
     *      }
     * 
     * This is like the following C# code:
     * 
     *      async Task<Response> requestExample(string url)
     *      {
     *          System.Console.WriteLine("Making network request...");
     * 
     *          Response result = await Network.request(url);
     * 
     *          if (200 == result.status)
     *          {
     *              return result;
     *          }
     *          else
     *          {
     *              throw new NetworkFailedException(result);
     *          }
     *      }
     * 
     * or the following TypeScript code:
     *
     *      async function requestExample(url: string): Promise<Response>
     *      {
     *          console.log("Making network request...");
     * 
     *          let result = await Network.request(url);
     *          
     *          if (200 == request.status)
     *          {
     *              return result;
     *          }
     *          else
     *          {
     *              throw new NetworkFailedException(result);
     *          }
     *      }
     * 
     * Notice that both languages also return their respective wrapper
     * type for all asynchronous functions. This is for API-compatibility
     * with other approaches. It is also important to keep in consideration!
     * 
     * PHP developers who are unfamiliar with asynchronous design may think
     * that an async function returns the type it returns, but it simply
     * provides syntax to work with Promises in a synchronous manner.
     * Remember that *ANY* function which needs to use the result of an
     * asynchronous function must also do so asynchronously, either by using
     * Promise::then() or implementing its body in an async stream.
     * 
     * @template T
     * @param callable<T> $cb
     * @return Promise<T>
     */
    public static function async/*<T>*/(callable/*<T>*/ $cb): Promise/*<T>*/
    {
        /*
         * Capture the result of the callback provided.
         * 
         * All anonymous functions are Closure objects, including those
         * that have the `yield` keyword. They only become Generator objects
         * after being ran for the first time.
         * 
         * As such, a check is needed immediately after this.
         */
        $result = $cb();

        if ($result instanceof Generator)
        {
            $async = new AsyncFunction($result);

            // Run the Generator for the first time. This will handle
            // the first `yield` expression and move on to the next.
            $async->run();

            return $async->getPromise();
        }
        else
        {
            // Return a Promise that instantly resolves with the result
            // of the callback.
            return new Promise(fn($r) => $r($result));
        }
    }

    /**
     * Synchronously get the result of a Promise (or throw an exeception).
     * 
     * As this is blocking, using this will have none of the benefits of an
     * asynchronous design.
     * 
     * This should be used rarely, and only for critical code.
     */
    public static function awaitSync(Promise $p): mixed
    {
        $result = null;

        // Registers the base handlers for the Promise.
        $p->then(function ($r) use (&$result) {
            $result = $r;
        })->catch(function ($e) {
            throw $e;
        });

        do
        {
            EventLoop::run();
        }
        while (PromiseStatus::PENDING == $p->status);

        return $result;
    }
}