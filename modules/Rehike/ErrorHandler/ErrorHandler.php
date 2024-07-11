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

use Rehike\Logging\LogFileManager;
use Rehike\Logging\ExceptionLogger;
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

    private static bool $isEnabled = true;
    private static AbstractErrorPage $pageModel;

    private static bool $hasLogFile = false;
    private static string $logFileName = "";

    public static function getErrorPageModel(): AbstractErrorPage
    {
        return self::$pageModel;
    }

    /**
     * Enables the global error handler. This is the default option.
     */
    public static function enable(): void
    {
        self::$isEnabled = true;
    }

    /**
     * Disables the global error handler.
     *
     * This should only be used during premature shutdown events.
     */
    public static function disable(): void
    {
        self::$isEnabled = false;
    }

    public static function handleUncaughtException(Throwable $e): void
    {
        if (!self::$isEnabled)
            return;

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

        try
        {
            LogFileManager::createExceptionLogFile(
                ExceptionLogger::getFormattedException($e)
            );
        }
        catch (\Throwable $e)
        {
            // The log file system is kinda poorly written and doesn't check
            // if certain modules are loaded before attempting to access
            // them, which can end up throwing an error in some cases.
            // For now, this will just be hacked around by ignoring any errors
            // that are thrown by the log file manager.
            // However, we will clear the latest log file name in order to
            // avoid referring to a nonexistent file, since the file is likely
            // to have not been created if an exception was thrown anywhere during
            // the execution there.
            self::$hasLogFile = false;
            self::$logFileName = "";
        }
        
        self::renderErrorTemplate();
        exit();
    }

    public static function handleShutdown(): void
    {
        if (!self::$isEnabled)
            return;

        $e = error_get_last();

        if ($e != null && isset($e["type"]) && self::isErrorFatal($e["type"]))
        {
            self::$pageModel = new FatalErrorPage($e);

            self::renderErrorTemplate();
            exit();
        }
    }

    public static function getHasLogFile(): bool
    {
        return self::$hasLogFile;
    }

    public static function getLogFileName(): string
    {
        return self::$logFileName;
    }

    public static function setLogFileName(string $name): void
    {
        self::$hasLogFile = true;
        self::$logFileName = $name;
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