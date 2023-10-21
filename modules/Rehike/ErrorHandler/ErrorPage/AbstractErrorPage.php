<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Logging\DebugLogger;

/**
 * Represents an abstract error page model.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AbstractErrorPage
{
    /**
     * Get the title of the error page type.
     */
    abstract public function getTitle(): string;

    /**
     * Gets a log of debug messages printed during the runtime session.
     * 
     * @return string[]
     */
    public function getDebugLog(): array
    {
        return DebugLogger::getLogs();
    }
}