<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Logging\Common\FormattedString;
use Rehike\Logging\ExceptionLogger;

use Throwable;

/**
 * Represents the error page for an uncaught exception.
 * 
 * @author The Rehike Maintainers
 */
class UncaughtExceptionPage extends AbstractErrorPage 
{
    private FormattedString $exceptionLog;

    public function __construct(Throwable $e)
    {
        $this->exceptionLog = ExceptionLogger::getFormattedException($e);
    }

    public function getTitle(): string
    {
        return "Uncaught exception";
    }

    /**
     * Get the exception log.
     */
    public function getExceptionLog(): FormattedString
    {
        return $this->exceptionLog;
    }
}