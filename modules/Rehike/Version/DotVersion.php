<?php
namespace Rehike\Version;

/**
 * Get version information from the .version file
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DotVersion
{
    /**
     * Determine if the version system can be used.
     */
    public static function canUse(): bool
    {
        return file_exists(".version");
    }

    /**
     * Return info from the .version file.
     * 
     * @return string[]
     */
    public static function getInfo(): array
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