<?php
namespace Rehike\Async;

const ASYNC_FUNCTION_FILE = __FILE__;

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
 * Usage example:
 * 
 *      function requestExample(string $url): Promise//<Response>
 *      {
 *          return async(function() use (&$myPromises) {
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
function async/*<T>*/(callable/*<T>*/ $cb): Promise/*<T>*/
{
    return Concurrency::async($cb);
}