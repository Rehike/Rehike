<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Logging\Common\FormattedString;
use Rehike\Logging\ExceptionLogger;
use Rehike\Exception\Network\InnertubeFailedRequestException;

use Throwable;

/**
 * Represents the error page for a failed InnerTube request (or the exception
 * class: InnertubeFailedRequestException).
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class InnertubeFailedRequestPage extends UncaughtExceptionPage
{
    private InnertubeFailedRequestException $innertubeFailedException;

    public function __construct(InnertubeFailedRequestException $e)
    {
        parent::__construct($e);
        $this->innertubeFailedException = $e;
    }

    public function getTitle(): string
    {
        return "Failed InnerTube request (probably YouTube server issue)";
    }

    public function getInnertubeFailedException(): InnertubeFailedRequestException
    {
        return $this->innertubeFailedException;
    }
}