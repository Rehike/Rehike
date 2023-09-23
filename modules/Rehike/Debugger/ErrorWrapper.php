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
    public int $errno;
    public string $errstr;
    public string $errfile;
    public int $errline;

    public function __construct(int $errno, string $errstr, string $errfile, int $errline)
    {
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        $this->errline = $errline;
    }
}