<?php
namespace Rehike\Version;

use DateTime, DateTimeZone;

/**
 * Control the retrival of version information.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class VersionController
{
    /**
     * Structure storing the version information.
     * 
     * This is obtained from the .version file in root, or .git if it is
     * available.
     */
    public static VersionInfo $versionInfo;

    public static function __initStatic(): void
    {
        self::init();
    }

    /**
     * Initialise the Version subsystem.
     */
    public static function init(): bool
    {
        static $hasRun = false;
        if ($hasRun) return true;

        self::$versionInfo = new VersionInfo;

        if ($dg = DotGit::canUse())
        {
            DotGit::getInfo(self::$versionInfo);
            self::$versionInfo->supportsDotGit = true;
        }
        
        if ($dv = DotVersion::canUse())
        {
            DotVersion::getInfo(self::$versionInfo);
        }

        if ($dg || $dv)
        {
            $hasRun = true;
            return true;
        }

        return false;
    }

    /**
     * Attempt to get all relevant information about the current version.
     */
    public static function getVersion(): string
    {
        $semanticVersion = \Rehike\Constants\VERSION;
        
        $dateTime = new DateTime(timezone: new DateTimeZone("GMT"));
        $dateAvailable = false;
        if (isset(self::$versionInfo->time) && is_int(self::$versionInfo->time))
        {
            $dateTime->setTimestamp(self::$versionInfo->time);
            $dateAvailable = true;
        }

        $initStatus = self::init();

        if ($initStatus && @self::$versionInfo->currentRevisionId && $dateAvailable)
        {
            $semanticVersion .= " (build " . (string)self::$versionInfo->currentRevisionId . ", " . $dateTime->format("Y-m-d") . ")";
        }

        return $semanticVersion;
    }
}