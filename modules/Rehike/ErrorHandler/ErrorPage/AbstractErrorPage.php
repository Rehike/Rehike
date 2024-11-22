<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Logging\DebugLogger;

/**
 * Represents an abstract error page model.
 * 
 * @author The Rehike Maintainers
 */
abstract class AbstractErrorPage
{
    protected bool $displayIssueTrackerLink = true;
    protected bool $displayMessageLogs = true;
    
    /**
     * Get the title of the error page type.
     */
    abstract public function getTitle(): string;
    
    public function shouldDisplayIssueTrackerLink(): bool
    {
        return $this->displayIssueTrackerLink;
    }
    
    public function shouldDisplayMessageLogs(): bool
    {
        return $this->displayMessageLogs;
    }

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