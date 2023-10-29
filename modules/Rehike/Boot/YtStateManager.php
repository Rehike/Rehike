<?php
namespace Rehike\Boot;

use Rehike\{
    YtApp,
    TemplateManager,
    ControllerV2\Core as ControllerV2,
    Debugger\Debugger,
    Signin\AuthManager,
    ConfigManager\Config
};

/**
 * Manages the global app state for Rehike during boot.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class YtStateManager
{
    /**
     * Initialise and get the global state.
     */
    public static function init(): YtApp
    {
        $yt = new YtApp();

        self::bindToEverything($yt);

        return $yt;
    }

    /**
     * Bind the global state to everything that needs it.
     */
    protected static function bindToEverything(YtApp $yt): void
    {
        TemplateManager::registerGlobalState($yt);
        ControllerV2::registerStateVariable($yt);
        Debugger::init($yt);

        /*
         * TODO: This should be removed when V1 is deprecated.
         */
        if (Config::getConfigProp("experiments.useSignInV2") !== true)
        {
            AuthManager::use($yt);
        }
    }
}