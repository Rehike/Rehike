<?php
namespace Rehike\Async\Promise;

/**
 * States for Promise resolution status.
 * 
 * Since this package targets PHP 7 and 8.0 as well, native
 * enums cannot be used.
 * 
 * When the time comes to deprecate PHP 8.0 support, this class
 * may be seamlessly moved to an 8.1 native enum by replacing consts
 * with cases. As such, please use the enum constant exclusively, never the
 * corresponding integer value, as it will make this transition harder.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PromiseStatus
{
    public const PENDING = 0;
    public const RESOLVED = 1;
    public const REJECTED = 2;
}