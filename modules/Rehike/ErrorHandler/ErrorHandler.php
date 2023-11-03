<?php
namespace Rehike\ErrorHandler;

use Throwable;

use Rehike\ErrorHandler\ErrorPage\{
    AbstractErrorPage,
    FatalErrorPage,
    UncaughtExceptionPage,
    UnhandledPromisePage,
    InnertubeFailedRequestPage
};

use Rehike\Exception\Network\InnertubeFailedRequestException;
use YukisCoffee\CoffeeRequest\Exception\UnhandledPromiseException;
use YukisCoffee\CoffeeRequest\Exception\UncaughtPromiseException;
use YukisCoffee\CoffeeRequest\Exception\PromiseAllException;

/**
 * Implements a general error handler for Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class ErrorHandler
{
    // These are aliases which look pretty in code later on, that's all!
    public const handleUncaughtException = self::class."::handleUncaughtException";
    public const handleShutdown = self::class."::handleShutdown";

    private static AbstractErrorPage $pageModel;

    public static function getErrorPageModel(): AbstractErrorPage
    {
        return self::$pageModel;
    }

    public static function handleUncaughtException(Throwable $e): void
    {
        if (class_exists("YukisCoffee\\CoffeeRequest\\Util\\PromiseResolutionTracker", false))
        {
            // Disable promise resolution tracker because it may trigger additional
            // error messages on top of our own.
            \YukisCoffee\CoffeeRequest\Util\PromiseResolutionTracker::disable();
        }

        // Unpack uncaught promise exceptions in order to handle them too:
        if ($e instanceof UncaughtPromiseException)
        {
            $promiseException = $e;
            $e = $e->getOriginal();
            $wasInPromise = true;
        }

        /*
         * BUG (kirasicecreamm): Unhandled Promises are not compatible with the custom 
         * error page yet because they are triggered during the cleanup at the end of
         * the script.
         * 
         * The Promise system itself will need to be modified to account for this.
         */
        if ($e instanceof UnhandledPromiseException)
        {
            self::$pageModel = new UnhandledPromisePage($e);
        }
        else if ($e instanceof InnertubeFailedRequestException)
        {
            self::$pageModel = new InnertubeFailedRequestPage($e);
        }
        else if ($e instanceof PromiseAllException)
        {
            if ($e->getReason() instanceof InnertubeFailedRequestException)
            {
                self::$pageModel = new InnertubeFailedRequestPage($e->getReason());
            }
            else
            {
                self::$pageModel = new UncaughtExceptionPage($e);
            }
        }
        else
        {
            self::$pageModel = new UncaughtExceptionPage($e);
        }

        self::renderErrorTemplate();
        exit();
    }

    public static function handleShutdown(): void
    {
        $e = error_get_last();

        if ($e != null && isset($e["type"]) && self::isErrorFatal($e["type"]))
        {
            self::$pageModel = new FatalErrorPage($e);

            self::renderErrorTemplate();
            exit();
        }
    }

    private static function renderErrorTemplate(): void
    {
        static $hasRendered = false;

        if ($hasRendered) return;

        while (ob_get_level() != 0)
        {
            ob_end_clean();
        }

        ob_start();
        require "includes/fatal_templates/fatal_error_page.html.php";
        ob_end_flush();

        $hasRendered = true;
    }

    private static function isErrorFatal(int $errorType): bool
    {
        return match ($errorType) {
            E_ERROR => true,
            E_CORE_ERROR => true,
            E_COMPILE_ERROR => true,
            E_PARSE => true,
            E_USER_ERROR => true,
            default => false
        };
    }
}

register_shutdown_function(ErrorHandler::handleShutdown);
set_exception_handler(ErrorHandler::handleUncaughtException);