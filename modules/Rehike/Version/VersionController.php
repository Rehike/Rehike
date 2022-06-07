<?php
namespace Rehike\Version;

/**
 * Control the retrival of version information.
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
    public static $versionInfo = [];

    public static function init()
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

    public static function getVersion()
    {
        $semanticVersion = "0.0"; // No semantic versioning system at the moment.

        $initStatus = self::init();

        if ($initStatus && @self::$versionInfo["currentRevisionId"])
        {
            $semanticVersion .= "." . (string)self::$versionInfo["currentRevisionId"];
        }

        return $semanticVersion;
    }
}