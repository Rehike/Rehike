<?php
namespace Rehike\Async\Exception;

use Rehike\Async\Promise;
use Rehike\Async\Debugging\PromiseStackTrace;
use Rehike\Attributes\Override;

use Exception;

/**
 * Represents an Exception that occurs within or regarding Promises.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PromiseException extends Exception
{
    protected Promise $relatedPromise;

    public function __construct(
            Promise $promise, 
            string $message = "", 
            int $code = 0, 
            $previous = null
    )
    {
        parent::__construct($message, $code, $previous);

        $this->relatedPromise = $promise;
    }

    public function getCustomTrace(): PromiseStackTrace
    {
        return $this->relatedPromise->latestTrace;
    }

    #[Override]
    public function __toString(): string
    {
        $class = get_called_class();
        $file = $this->getCustomTrace()->getTraceAsArray()[0]["file"];
        $line = $this->getCustomTrace()->getTraceAsArray()[0]["line"];

        $result = "$class (in promise): $this->message in $file:$line\n";
        $result .= "Stack trace:\n";
        $result .= (string)$this->getCustomTrace();
        $result .= "\n\nFull trace:\n";
        $result .= $this->getCustomTrace()->getOriginalTraceAsString();

        return $result;
    }
}