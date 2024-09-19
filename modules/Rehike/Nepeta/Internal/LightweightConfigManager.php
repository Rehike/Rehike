<?php
namespace Rehike\Nepeta\Internal;

/**
 * Lightweight config manager for Nepeta mods.
 * 
 * Nepeta loads very early on, so the Rehike config manager isn't available yet.
 * We have the startup config available for very early access purposes, but we
 * are otherwise fucked.
 * 
 * Ideally, we'd want Nepeta to be able to hook the general ConfigManager\Config
 * class, so we don't use that at all. Instead, we have a completely independent
 * configuration class here.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class LightweightConfigManager
{
    private static ?object $config = null;

    public static function init(): void
    {
        // This global variable is defined in includes/startup_config.php for
        // early config access.
        global $g_rehikeStartupConfig;

        self::$config = &$g_rehikeStartupConfig;
    }

    public static function isAvailable(): bool
    {
        return null !== self::$config;
    }

    /**
     * Gets a property at the provided path.
     */
    public static function getProp(string $propPath): mixed
    {
        $target = self::$config;

        $props = explode(".", $propPath);

        foreach ($props as $prop)
        {
            if (isset($target->{$prop})) // No safeguards needed here
            {
                $target = $target->{$prop};
            }
            else if (
                (is_array($target) || $target instanceof \ArrayAccess) && 
                isset($target[$prop])
            )
            {
                $target = $target[$prop];
            }
            else
            {
                break;
            }
        }

        return $target == self::$config ? null : $target;
    }
}