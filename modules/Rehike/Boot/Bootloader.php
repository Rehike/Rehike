<?php
namespace Rehike\Boot;

use Rehike\{
    Debugger\Debugger,
    DisableRehike\DisableRehike
};
use Rehike\ConfigManager\Config;

/**
 * Main bootstrapper insertion point for Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class Bootloader
{
    /**
     * Start a new Rehike session.
     */
    public static function startSession(): void
    {
        \Rehike\Profiler::start("rhboot");
        self::boot();
        \Rehike\Profiler::end("rhboot");
        self::postboot();
        self::shutdown();
    }

    /**
     * Performs an early shutdown.
     */
    public static function doEarlyShutdown(): void
    {
        // Silence shutdown errors such as the unhandled promise error
        if (class_exists("YukisCoffee\\CoffeeRequest\\Promise", false))
        {
            if (\YukisCoffee\CoffeeRequest\Promise::isCurrentlyInPromise())
            {
                if (class_exists("YukisCoffee\\CoffeeRequest\\Util\\PromiseResolutionTracker", false))
                {
                    \YukisCoffee\CoffeeRequest\Util\PromiseResolutionTracker::disable();
                }
            }
        }

        if (class_exists("Rehike\\ErrorHandler\\ErrorHandler"))
        {
            \Rehike\ErrorHandler\ErrorHandler::disable();
        }

        // Perform general shutdown tasks.
        self::shutdown(true);

        // Close the server
        exit();
    }

    /**
     * Sets up everything necessary to load a Rehike page.
     */
    private static function boot(): void
    {
        self::runInitTasks();

        $yt = YtStateManager::init();

        self::runSetupTasks();
    }

    /**
     * Manages main application behaviour after the initial boot process is
     * done.
     */
    private static function postboot(): void
    {
        if (DisableRehike::shouldDisable())
        {
            DisableRehike::disableForSession();
        }
        else
        {
            if (DisableRehike::shouldPersistentlyEnableRehikeFromCurrentUrl())
            {
                DisableRehike::enableRehike();
            }
            
            require "router.php";
        }
    }

    /**
     * Ran after all page logic is done.
     */
    private static function shutdown(bool $early = false): void
    {
        Debugger::shutdown();
        
        if (Config::getConfigProp("hidden.enableProfiler"))
        {
            header(
                "X-Rehike-Profiler-Result: " . 
                json_encode(\Rehike\Profiler::getTimings())
            );
        }

        if (ob_get_level() > 1)
            ob_end_flush();
    }

    /**
     * Runs all initialisation tasks.
     * 
     * These are early-stage configuration tasks that later "setup" tasks
     * may be dependent upon. These do not have access to the global state.
     */
    private static function runInitTasks(): void
    {
        Tasks::initNetwork();
        Tasks::initResourceConstants();
        Tasks::initConfigManager();
    }

    /**
     * Runs all setup tasks.
     * 
     * These are late-stage configuration tasks that have access to Rehike's
     * global state.
     */
    private static function runSetupTasks(): void
    {
        Tasks::setupTemplateManager();
        Tasks::setupI18n();
        Tasks::setupControllerV2();
        Tasks::setupPlayer();

        // The visitor data setup requires the player to be initialized, so it must
        // go last.
        Tasks::setupVisitorData();
    }
}