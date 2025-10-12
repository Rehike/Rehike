<?php
namespace Rehike\ErrorHandler;

use Throwable;

use Rehike\ErrorHandler\ErrorPage\{
    AbstractErrorPage,
    FailedToWriteConfigPage,
    FatalErrorPage,
    UncaughtExceptionPage,
    UnhandledPromisePage,
    InnertubeFailedRequestPage,
    PromiseAllExceptionPage
};

use Rehike\Logging\LogFileManager;
use Rehike\Logging\ExceptionLogger;
use Rehike\Exception\Network\InnertubeFailedRequestException;
use Rehike\Async\Exception\UnhandledPromiseException;
use Rehike\Async\Exception\UncaughtPromiseException;
use Rehike\Async\Exception\PromiseAllException;

/**
 * Implements a general error handler for Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
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
    
    /**
     * Determines if we are allowed to render for Twig.
     * 
     * This is true by default.
     */
    private static bool $allowRenderForTwig = true;

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
    
    public static function reportFailedToWriteConfig(): void
    {
        self::exileAsyncErrorHandling();
        
        self::$pageModel = new FailedToWriteConfigPage();
        self::renderErrorTemplate();
        exit();
    }

    public static function handleUncaughtException(Throwable $e): void
    {
        if (!self::$isEnabled)
            return;
        
        self::exileAsyncErrorHandling();

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
                self::$pageModel = new PromiseAllExceptionPage($e);
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
    
    /**
     * @internal
     */
    public static function shouldRenderForTwig(): bool
    {
        // We will render for Twig if the Twig context is sufficiently set-up:
        return self::$allowRenderForTwig &&
            class_exists("Rehike\\YtApp") &&
            class_exists("Rehike\\Debugger\\Debugger") && // Debugger is required for render
            class_exists("Rehike\\TemplateManager") &&
            class_exists("Twig\\Environment");
    }
    
    private static function exileAsyncErrorHandling(): void
    {
        if (class_exists("Rehike\\Async\\Promise\\PromiseResolutionTracker", false))
        {
            // Disable promise resolution tracker because it may trigger additional
            // error messages on top of our own.
            \Rehike\Async\Promise\PromiseResolutionTracker::disable();
        }
    }

    private static function renderErrorTemplate(): void
    {
        static $hasRendered = false;

        if ($hasRendered) return;
        
        // Render for Twig if the Twig template is set up.

        while (ob_get_level() != 0)
        {
            ob_end_clean();
        }

        ob_start();
        require "includes/fatal_templates/fatal_error_page.html.php";
        
        if (self::shouldRenderForTwig())
        {
            try
            {
                // Prepare the page context:                
                $pageContents = ob_get_clean();
                \Rehike\YtApp::getInstance()->page = (object)[
                    "errorHtml" => $pageContents
                ];
                \Rehike\YtApp::getInstance()->title = "Rehike fatal error";
                
                // Check if we were navigating via SPF (if SPF is set up):
                if (class_exists("Rehike\\Spf\\Spf") && \Rehike\Spf\Spf::isSpfRequested())
                {
                    // We pre-serialize Rebug data via SPF because it doesn't encode
                    // anything in the Twig context itself. This is probably due to
                    // recursion, but I don't care to look into it. - Taniko Yamamoto (2023/11/15)
                    \Rehike\YtApp::getInstance()->spfConfig->rebugData = json_encode(\Rehike\Debugger\Debugger::exposeSpf());
                    
                    \Rehike\YtApp::getInstance()->spf = true;
                    header("Content-Type: application/json");
                }
                else
                {
                    // We need to expose the debugger in order to render a page.
                    \Rehike\Debugger\Debugger::expose();
                }
                
                echo \Rehike\TemplateManager::render([], "fatal_error_stub");
            }
            catch (Throwable $e)
            {
                // Re-render without rendering for Twig being allowed if any error occurred trying
                // to render with Twig.
                \Rehike\Logging\DebugLogger::print("??? %s", $e->getMessage());
                self::$allowRenderForTwig = false;
                self::renderErrorTemplate();
                return;
            }
        }
        else
        {
            ob_end_flush();
        }

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