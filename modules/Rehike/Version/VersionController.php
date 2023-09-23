<?php
namespace Rehike\Version;

/**
 * Control the retrival of version information.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class VersionController
{
    /**
     * Array storing the version information.
     * 
     * This is obtained from the .version file in root,
     * or .git if it is available.
     * 
     * @var mixed[]
     */
    public static array $versionInfo = [];

    /**
     * Initialise the Version subsystem.
     */
    public static function init(): bool
    {
        static $hasRun = false;
        if ($hasRun) return true;

        if ($dg = DotGit::canUse())
        {
            self::$versionInfo = DotGit::getInfo();
            self::$versionInfo += ["supportsDotGit" => true];
        }
        
        if ($dv = DotVersion::canUse())
        {
            self::$versionInfo += DotVersion::getInfo();
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

        $initStatus = self::init();

        if ($initStatus && @self::$versionInfo["currentRevisionId"])
        {
            $semanticVersion .= "." . (string)self::$versionInfo["currentRevisionId"];
        }

        return $semanticVersion;
    }
}