<?php
namespace Rehike\Async\Exception;

use Rehike\Async\Promise;
use Rehike\Async\Debugging\PromiseStackTrace;

/**
 * Thrown when a Promise is unhandled within a session.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class UnhandledPromiseException extends PromiseException
{
    private PromiseStackTrace $originTrace;

    public function __construct(Promise $promise, PromiseStackTrace $originTrace)
    {
        parent::__construct($promise, "");
        $this->originTrace = $originTrace;
    }

    public function __toString(): string
    {
        $promise = $this->relatedPromise;
        $originTrace = $this->originTrace;

        $promiseName = get_class($promise);
        
        $firstFile = self::formatErrorFile($promise->creationTrace->getTraceAsArray()[0]);
        $latestFile = self::formatErrorFile($originTrace->getTraceAsArray()[0]);

        if ($firstFile != $latestFile)
        {
            $source = "from $firstFile in $latestFile";
        }
        else
        {
            $source = "in $latestFile";
        }

        $errorMsg = "Unhandled Promise($promiseName) $source\n" . 
                    "Stack trace:\n" . $originTrace->getTraceAsString() . "\n\n" .
                    "Full trace:\n" . $originTrace->getOriginalTraceAsString()
        ;

        do
        {
            ob_end_clean();
        }
        while (ob_get_level() > 0);

        header("Content-Type: text/plain");
        echo $errorMsg;

        return "";
    }

    private static function formatErrorFile(array $traceItem): string
    {
        $fileName = $traceItem["file"];
        $lineNumber = $traceItem["line"];

        $formattedFile = "[unknown file]";
        
        if (is_string($fileName))
        {
            $formattedFile = $fileName;

            if (is_int($lineNumber))
            {
                $formattedFile .= ":$lineNumber";
            }
        }

        return $formattedFile;
    }
}