<?php
namespace Rehike\Network\Enum;

/**
 * Redirect policy enum for the Rehike network library.
 * 
 * Since this package targets PHP 8.0 as well, native
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
class RedirectPolicy
{
    public const FOLLOW = 0;
    public const MANUAL = 1;
}