<?php
namespace Rehike\Exception;

/**
 * Base exception class for most Rehike exceptions.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractException extends \Exception
{
    private $trace;

    /**
     * Create a new exception from a previously thrown
     * one.
     */
    public static function from(\Throwable $exception): static
    {
        $instance = new static(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );

        $instance->_setFile( $exception->getFile() );
        $instance->_setLine( $exception->getLine() );
        $instance->_setTrace( $exception->getTrace() );

        return $instance;
    }

    private function _setFile($a): void
    {
        $this->file = $a;
    }

    private function _setLine($a): void
    {
        $this->line = $a;
    }

    private function _setTrace($a): void
    {
        $this->trace = $a;
    }
}