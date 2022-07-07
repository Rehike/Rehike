<?php
namespace Rehike\Debugger;

/**
 * Implements a general error wrapper class.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class ErrorWrapper
{
    public $errno;
    public $errstr;
    public $errfile;
    public $errline;

    public function __construct($errno, $errstr, $errfile, $errline)
    {
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        $this->errline = $errline;
    }
}