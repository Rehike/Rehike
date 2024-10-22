<?php
namespace Rehike\Network\Exception;

use Exception;

/**
 * Used to implement an exception type that has a hard-coded message.
 * 
 * Thus, no arguments are used to construct one. This is sometimes
 * useful where exception types tend not to differ.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
abstract class BaseHardMessageException extends Exception
{
    protected const MESSAGE = "";

    public function __construct()
    {
        parent::__construct(static::MESSAGE);
    }
}