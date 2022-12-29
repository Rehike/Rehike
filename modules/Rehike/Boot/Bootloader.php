<?php
namespace Rehike\Boot;

use Rehike\{
    Debugger\Debugger
};

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
        self::boot();
        self::postboot();
        self::shutdown();
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
        require "router.php";
    }

    /**
     * Ran after all page logic is done.
     */
    private static function shutdown(): void
    {
        Debugger::shutdown();
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
        Tasks::setupVisitorData();
    }
}