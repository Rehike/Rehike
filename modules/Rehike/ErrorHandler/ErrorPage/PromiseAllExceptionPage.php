<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Async\Exception\PromiseAllException;
use Rehike\Logging\Common\FormattedString;
use Rehike\Logging\ExceptionLogger;

use Throwable;

/**
 * Represents the error page for an uncaught exception within a Promise::all
 * context.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class PromiseAllExceptionPage extends UncaughtExceptionPage 
{
    private FormattedString $innerExceptionLog;

    public function __construct(PromiseAllException $e)
    {
        parent::__construct($e);
        $this->innerExceptionLog = ExceptionLogger::getFormattedException($e->getReason());
    }

    /**
     * Get the exception log.
     */
    public function getInnerExceptionLog(): FormattedString
    {
        return $this->innerExceptionLog;
    }
}