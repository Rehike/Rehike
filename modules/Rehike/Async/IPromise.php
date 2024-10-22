<?php
namespace Rehike\Async;

use Rehike\Async\Debugging\IPromiseStackTrace;
use Rehike\Async\Promise\PromiseStatus;

use Throwable;

/**
 * Interface for Promise objects.
 * 
 * @property PromiseStatus $status  Represents the current status of a promise.
 * @property T $result  The promise result. This should only be accessed if the
 *                      promise is resolved.
 * @property Throwable $reason  The promise failure reason. This should only be
 *                              accessed if the promise is rejected.
 * @property IPromiseStackTrace $creationTrace  Stack trace at promise creation.
 * @property IPromiseStackTrace $latestTrace  Stack trace as of the latest
 *                                            update to the object.
 * 
 * @template T
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
interface IPromise/*<T>*/
{
    /**
     * Register a function to be called upon a promise's
     * resolution.
     * 
     * @param callable<mixed>
     * @return IPromise<T>
     */
    public function then(callable/*<mixed>*/ $cb): IPromise/*<T>*/;

    /**
     * Register a function to be called upon an error occurring
     * during a promise's resolution.
     * 
     * @param callable<Throwable> $cb
     * @return IPromise<T>
     */
    public function catch(callable/*<Throwable>*/ $cb): IPromise/*<T>*/;

    /**
     * Resolve a promise.
     * 
     * @param T $data
     */
    public function resolve(/*T*/ $data = null): void;

    /**
     * Reject a Promise (error).
     * 
     * @param string|Throwable $e (union types are PHP 8.0+)
     * 
     * @internal
     * @param 
     */
    public function reject($e): void;
}