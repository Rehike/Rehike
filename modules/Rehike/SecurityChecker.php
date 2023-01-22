<?php
namespace Rehike;

/**
 * Manages security checks.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SecurityChecker
{
    private static bool $isSecure = true;
    private static int $securityFactor = 0;

    public static function __initStatic()
    {
        $securityFactor = 0;

        if ("windows nt" == strtolower(php_uname('s')))
        {
            $securityFactor |= 2 * (int)self::windowsNtIsRunningAsSystem();
        }

        self::$securityFactor = $securityFactor;
        self::$isSecure = $securityFactor == 0;
    }

    public static function isSecure(): bool
    {
        return self::$isSecure;
    }

    public static function windowsNtIsRunningAsSystem($dontLie = false): bool
    {
        $disabled = $dontLie || (RehikeConfigManager::getConfigProp(
            "hidden.securityIgnoreWindowsServerRunningAsSystem"
        ) == true);

        if (!$disabled)
        {
            $currentUser = @get_current_user();

            if ("SYSTEM" == trim($currentUser))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}