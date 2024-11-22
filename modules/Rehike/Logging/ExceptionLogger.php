<?php
namespace Rehike\Logging;

use Rehike\FileSystem;
use Rehike\Logging\Common\FormattedString;

use Throwable;

/**
 * Formats printable exception logs that are more advanced than the default PHP
 * implementation.
 * 
 * The log format returned by this class are very similar to Java's exception
 * logs. Most of the implementation is inspired by this post:
 * https://www.php.net/manual/en/exception.gettraceasstring.php#114980
 * 
 * @author The Rehike Maintainers
 */
class ExceptionLogger
{
    // Disallow instantiation
    private function __construct() {}

    /**
     * Reformat a given exception in the custom format.
     */
    public static function getFormattedException(Throwable $e): FormattedString
    {
        $output = new FormattedString;
        $exceptions = self::getExceptionChain($e);
        $visitedTraces = [];

        foreach ($exceptions as $index => $currentException)
        {
            if ($index != 0)
            {
                $output->addText("\n");
                $output->addTaggedText("Thrown by ", "thrown_by");
            }

            self::formatException(
                $currentException, 
                $output, 
                $visitedTraces,
                $index != 0 // first one should not be truncated
            );
        }

        return $output;
    }

    private static function getExceptionChain(Throwable $e): array
    {
        // Always just add the initial exception since it will never be a
        // previous one:
        $out = [$e];

        // Work through the object nest and flatten it into the above array:
        $currentException = $e->getPrevious();

        while (null != $currentException)
        {
            $out[] = $currentException;
            $currentException = $currentException->getPrevious();
        }

        return $out;
    }

    private static function formatException(
            Throwable $e, 
            FormattedString $out, 
            array &$visitedTraces,
            bool $truncateLog = true
    ): void
    {
        $trace = $e->getTrace();
        $file = FileSystem::getRehikeRelativePath($e->getFile());
        $line = $e->getLine();
        $skipArgs = false;
        $message = $e->getMessage();

        $out->addTaggedText(get_class($e), "exception_class");
        $out->addText(": ");
        $out->addTaggedText($message, "exception_message");

        while (true)
        {
            $out->addText("\n");

            $currentPos = "$file:$line";

            // If the log should be truncated, which we want to do with ones other
            // than the first log, then truncate:
            if ($truncateLog && in_array($currentPos, $visitedTraces))
            {
                $out->addTaggedText(
                    sprintf(
                        "  ... %d more",
                        count($trace) + 1
                    ), 
                    "more_exceptions"
                );

                break;
            }

            $out->addTaggedText("  at ", "at_text");
            
            if (count($trace) > 0 && array_key_exists("class", $trace[0]))
            {
                $tmpClassName = $trace[0]["class"];

                // Parse class name to prettify identifiers for anonymous classes:
                if (preg_match(
                    "/@anonymous(.*?)\\$([a-zA-Z0-9]+)/", 
                    $tmpClassName, 
                    $tmpClassNameMatches
                ))
                {
                    // Remove text
                    $tmpClassName = str_replace(
                        $tmpClassNameMatches[1], 
                        "",
                        $tmpClassName
                    );

                    $tmpClassName = str_replace(
                        "@anonymous$",
                        "@anonymous(#",
                        $tmpClassName
                    );

                    $tmpClassName .= ")";

                    $anonymizedPath = FileSystem::getRehikeRelativePath(
                        $tmpClassNameMatches[1]
                    );

                    $tmpClassName .= "<$anonymizedPath>";
                }

                $out->addTaggedText($tmpClassName, "class_name");

                if (array_key_exists("function", $trace[0]))
                {
                    $out->addTaggedText("::", "double_colon_operator");
                    $out->addTaggedText($trace[0]["function"], "method_name");
                }
            }
            else if (count($trace) > 0 && array_key_exists("function", $trace[0]))
            {
                $out->addTaggedText($trace[0]["function"], "function_name");
            }
            else
            {
                // Arguments should not be shown on main, as a stylistic choice.
                $skipArgs = true;
                $out->addTaggedText("{main}", "function_name");
            }

            if (!$skipArgs)
            {
                $out->addTaggedText("(", "args_parentheses");

                if (count($trace) > 0 && array_key_exists("args", $trace[0]))
                {
                    $numArgs = count($trace[0]["args"]);

                    foreach ($trace[0]["args"] as $i => $arg)
                    {
                        switch (gettype($arg))
                        {
                            case "string":
                                $tmpStringText = str_replace("\"", "\\\"", $arg);

                                if (strlen($tmpStringText) > 255)
                                {
                                    $tmpStringText = substr($tmpStringText, 0, 255 - 3) . "...";
                                }

                                $out->addTaggedText(
                                    "\"" . $tmpStringText . "\"",
                                    "args_type_string"
                                );
                                break;
                            case "integer":
                            case "double":
                                $out->addTaggedText(
                                    $arg,
                                    "args_type_number"
                                );
                                break;
                            case "array":
                                $out->addTaggedText(
                                    "array<" . count($arg) . '>',
                                    "args_type_array"
                                );
                                break;
                            case "object":
                                $out->addTaggedText(
                                    get_class($arg),
                                    "args_type_object"
                                );
                                break;
                            case "resource":
                                $out->addTaggedText(
                                    "<resource>",
                                    "args_type_resource"
                                );
                                break;
                            case "resource (closed)":
                                $out->addTaggedText(
                                    "<resource (closed)>",
                                    "args_type_resource"
                                );
                                break;
                            case "NULL":
                                $out->addTaggedText(
                                    "null",
                                    "args_type_null"
                                );
                                break;
                            default:
                                $out->addTaggedText(
                                    "<unknown type>",
                                    "args_type_unknown"
                                );
                                break;
                        }

                        if ($i != $numArgs - 1)
                        {
                            $out->addTaggedText(", ", "args_comma");
                        }
                    }
                }

                $out->addTaggedText(")", "args_parentheses");
            }

            $out->addTaggedText("(", "file_line_parentheses");
            $out->addTaggedText($currentPos, "file_line");
            $out->addTaggedText(")", "file_line_parentheses");

            $visitedTraces[] = $currentPos;

            if (count($trace) <= 0)
            {
                break;
            }

            $file = array_key_exists("file", $trace[0])
                ? FileSystem::getRehikeRelativePath($trace[0]["file"])
                : "{unknown}";
            $line = array_key_exists("file", $trace[0]) && array_key_exists("line", $trace[0]) && $trace[0]["line"]
                ? $trace[0]["line"] 
                : null;
            array_shift($trace);
        }
    }
}