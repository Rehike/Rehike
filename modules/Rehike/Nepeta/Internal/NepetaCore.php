<?php
namespace Rehike\Nepeta\Internal;

use Rehike\ConfigManager\Config;

/**
 * Provides core services for the Nepeta extensions system.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class NepetaCore
{
    /**
     * The folder name in which extensions are stored.
     */
    public const NEPETA_EXT_PATH = "nepeta_test";

    private static ?NepetaTheme $currentTheme = null;

    /**
     * Performs early startup services.
     */
    public static function init(): void
    {
        // Startup the lightweight config manager, as it's needed by the rest of
        // the initialization process:
        LightweightConfigManager::init();

        // Startup the package manager:
        PackageManager::init();
    }

    /**
     * Checks if Nepeta is enabled.
     */
    public static function isNepetaEnabled(): bool
    {
        return true == Config::getConfigProp("experiments.enableNepeta");
    }

    /**
     * Get the current theme set by the user.
     */
    public static function getTheme(): ?NepetaTheme
    {
        return self::$currentTheme;
    }

    /**
     * Sets the loaded theme.
     */
    public static function setTheme(NepetaTheme $theme)
    {
        self::$currentTheme = $theme;
    }
}