<?php
namespace Rehike\ErrorHandler\ErrorPage;

use Rehike\Logging\Common\FormattedString;
use Rehike\Logging\ExceptionLogger;

use Throwable;

/**
 * Represents the error page for an unhandled Promise.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class UnhandledPromisePage extends UncaughtExceptionPage
{
    public function getTitle(): string
    {
        return "Unhandled Promise";
    }
}