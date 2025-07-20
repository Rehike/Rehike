<?php
namespace Rehike\Async\Promise;

/**
 * Interface for promise objects which provide stack trace information.
 * 
 * @property IPromiseStackTrace $creationTrace  Stack trace at promise creation.
 * @property IPromiseStackTrace $latestTrace  Stack trace as of the latest
 *                                            update to the object.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
interface IPromiseWithStackTrace
{
}