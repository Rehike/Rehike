<?php
namespace Rehike\Async\Exception;

use Throwable;
use Exception;

/**
 * Triggered when a Promise::all() call is rejected.
 * 
 * This is typically because a Promise that it awaits is rejected. As
 * such, this takes two arguments: a general message from Promise::all()
 * incidating the index of the failed Promise, as well as the reason for 
 * that Promise's rejection.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class PromiseAllException extends Exception
{
    private Throwable $subexception;

    public function __construct(string $reason, Throwable $subexception)
    {
        parent::__construct($reason);
        $this->subexception = $subexception;
    }

    public function getReason(): Throwable
    {
        return $this->subexception;
    }
}