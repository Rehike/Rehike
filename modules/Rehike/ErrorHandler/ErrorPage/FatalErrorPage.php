<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\FileSystem;

/**
 * Represents the page for a fatal PHP error.
 * 
 * @author The Rehike Maintainers
 */
class FatalErrorPage extends AbstractErrorPage 
{
    private string $type;
    private string $file;
    private ?string $message;

    public function __construct(array $phpError)
    {
        $this->type = $this->serializeType($phpError["type"] ?? 0);
        $this->message = $phpError["message"] ?? null;

        if ($phpError["file"] && $phpError["line"])
        {
            $this->file = FileSystem::getRehikeRelativePath($phpError["file"]) . 
                ":" . $phpError["line"];
        }
        else if ($phpError["file"])
        {
            $this->file = FileSystem::getRehikeRelativePath($phpError["file"]);
        }
        else
        {
            $this->file = "Unknown file";
        }
    }

    public function getTitle(): string
    {
        return "Fatal error";
    }

    /**
     * Get a user-friendly string representing the top of PHP error that
     * occurred.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the file and line on which PHP reports the error occurred.
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Check if I have a message to display!
     */
    public function hasMessage(): bool
    {
        return null != $this->message;
    }

    /**
     * Get the message for the error, or an empty string if there is none.
     */
    public function getMessage(): string
    {
        return $this->message ?? "";
    }

    /**
     * Get a user-friendly string describing the meaning of a given PHP error
     * type.
     */
    private function serializeType(int $errorType): string
    {
        return match ($errorType) {
            E_ERROR => "PHP engine runtime error (E_ERROR)",
            E_WARNING => "PHP engine runtime warning (E_WARNING)",
            E_PARSE => "PHP compiler parse error (E_PARSE)",
            E_NOTICE => "PHP engine runtime notice (E_NOTICE)",
            E_CORE_ERROR => "PHP engine error (E_CORE_ERROR)",
            E_CORE_WARNING => "PHP engine warning (E_CORE_WARNING)",
            E_COMPILE_ERROR => "PHP compiler error (E_COMPILE_ERROR)",
            E_COMPILE_WARNING => "PHP compiler warning (E_COMPILE_WARNING)",
            E_USER_ERROR => "Runtime error (E_USER_ERROR)",
            E_USER_WARNING => "Runtime warning (E_USER_WARNING)",
            E_USER_NOTICE => "Runtime notice (E_USER_NOTICE)",
            E_STRICT => "E_STRICT",
            E_RECOVERABLE_ERROR => "Recoverable error (E_RECOVERABLE_ERROR)",
            E_DEPRECATED => "PHP engine deprecation warning (E_DEPRECATED)",
            E_USER_DEPRECATED => "Deprecation warning (E_USER_DEPRECATED)",
            E_ALL => "E_ALL",
            default => "Unknown type"
        };
    }
}