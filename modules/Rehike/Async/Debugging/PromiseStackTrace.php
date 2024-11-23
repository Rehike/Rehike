<?php
namespace Rehike\Async\Debugging;

// An exception is used to get the top-level stacktrace.
use Exception;

/**
 * Implements a simplified stack trace for Promises.
 * 
 * This makes user debugging easier as it prioritises the actual direction of
 * the Promise in user-implemented code while hiding abstractions made within
 * this library.
 * 
 * As such, the stack trace in debug outputs can be made less misleading. The
 * programmer will not see anything about Loop.php in a Promise exception, and
 * will instead first see the last known file with the Promise, which is
 * considerably more useful.
 * 
 * The original (full) stack trace is preserved for advanced readings.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PromiseStackTrace implements IPromiseStackTrace
{
    /**
     * Stores the original stack trace at the time of construction.
     * 
     * The simplified stack trace is only formed when called upon, so only this
     * is stored.
     */
    private array $originalTrace;

    private static array $skippedFiles = [];

    /**
     * Registers a filename (class) to be skipped in reading the stack trace of
     * the Promise.
     * 
     * Since knowing the origin of the error is more important than knowing the
     * underlying behaviour in most cases, the primary stack trace shown is
     * simplified in order to prioritise the desired information.
     * 
     * By default, these are all classes that are involved in abstractions with
     * the Promise system.
     */
    public static function registerSkippedFile(string $filename): void
    {
        self::$skippedFiles[] = $filename;
    }

    /**
     * Unregisters a filename (class) to be skipped in reading the stack trace.
     * 
     * If the file is not present in the list, this will be skipped silently.
     */
    public static function unregisterSkippedFile(string $filename): void
    {
        if ($pos = array_search($filename, self::$skippedFiles))
        {
            array_splice(self::$skippedFiles, $pos, 1);
        }
    }

    public function __construct()
    {
        $this->originalTrace = (new Exception)->getTrace();
    }

    public function __toString()
    {
        return $this->getTraceAsString();
    }

    /**
     * Gets the simplified trace as an array.
     */
    public function getTraceAsArray(): array
    {
        $result = [];

        foreach ($this->originalTrace as $item)
        {
            if (!in_array($item["file"], self::$skippedFiles))
            {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Gets the simplified trace as a string.
     */
    public function getTraceAsString(): string
    {
        return self::stringifyTrace($this->getTraceAsArray());
    }

    /**
     * Gets the original (advanced) trace as an array.
     */
    public function getOriginalTraceAsArray(): array
    {
        return $this->originalTrace;
    }

    /**
     * Gets the original (advanced) trace as a string.
     */
    public function getOriginalTraceAsString(): string
    {
        return self::stringifyTrace($this->getOriginalTraceAsArray());
    }

    /**
     * Creates a stringified stack trace (similar to PHP's Exceptions).
     */
    private static function stringifyTrace(array $trace): string
    {
        $result = "";

        $i = 0;
        foreach ($trace as $item)
        {
            $file = $item["file"];
            $line = $item["line"];
            $function = $item["function"];

            if (!is_string($file))
            {
                $file = "[unknown file]";
            }

            if (isset($line) && is_int($line))
            {
                $file .= "($line)";
            }

            if (!is_string($function))
            {
                $result .= "#$i [internal function]";
                continue;
            }
            else
            {
                $result .= "#$i $file: $function(";
            }

            if (!empty($item["args"]))
            {
                $args = $item["args"];

                $alreadyHasArgument = false;
                foreach ($args as $argument)
                {
                    // If there's already an argument, add ", " for formatting.
                    if ($alreadyHasArgument)
                    {
                        $result .= ", ";
                    }
                    
                    switch (gettype($argument))
                    {
                        case "string":
                            $result .= '"';

                            $formattedString = str_replace('"', "\\\"", $argument);
                            if (strlen($formattedString) > 10)
                            {
                                $formattedString = substr($formattedString, 0, 10) . "...";
                            }
                            $result .= $formattedString;

                            $result .= '"';
                            break;
                        case "integer":
                            $result .= $argument;
                            break;
                        case "double":
                            $result .= sprintf("%lf", $argument);
                            break;
                        case "boolean":
                            $result .= $argument ? "true" : "false";
                            break;
                        case "object":
                            $result .= "Object(" . get_class($argument) . ")";
                            break;
                        case "array":
                            $result .= "Array";
                            break;
                        case "NULL":
                            $result .= "null";
                            break;
                        case "resource":
                        case "resource (closed)":
                            $result .= "Resource id #" . get_resource_id($argument);
                            break;
                        case "unknown type":
                            $result .= "[unknown type]";
                            break;
                    }

                    $alreadyHasArgument = true;
                }
            }

            $result .= ")\n";

            $i++;
        }

        $result .= "#$i {main}\n";

        return $result;
    }
}