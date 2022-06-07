<?php
namespace Rehike\Version;

/**
 * Get version information from the .version file
 */
class DotVersion
{
    public static function canUse()
    {
        return file_exists(".version");
    }

    public static function getInfo()
    {
        if (!self::canUse()) return []; // Add nothing

        $versionFile = file_get_contents(".version");

        $json = json_decode($versionFile);

        if (null == $json)
        {
            return [];
        }

        return (array)$json;
    }
}