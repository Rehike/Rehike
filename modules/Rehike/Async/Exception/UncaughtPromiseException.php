<?php
namespace Rehike\Async\Exception;

use Rehike\Attributes\Override;

use Exception;

/**
 * Thrown when any exception is uncaught in a Promise.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class UncaughtPromiseException extends Exception
{
    private Exception $original;
    private string $class;

    private function __construct(Exception $original, string $class)
    {
        $this->original = $original;
        $this->class = $class;
    }

    #[Override]
    public function __toString(): string
    {
        $class = $this->original::class;
        $message = $this->original->__toString();

        return preg_replace("/$class:/", "$class (in promise):", $message, 1)
            ?? "(in promise) " . $message;
    }

    public function getOriginal(): Exception
    {
        return $this->original;
    }

    public static function from(Exception $e): UncaughtPromiseException
    {
        $className = $e::class;

        return new self($e, $className);
    }
}