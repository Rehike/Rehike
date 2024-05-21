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
    public static function getInfo(VersionInfo $info): void
    {
        if (!self::canUse()) return; // Add nothing

        $versionFile = file_get_contents(".version");

        $json = json_decode($versionFile);
        
        if (null == $json)
        {
            $json = MessyJsonParser::parse($versionFile);
        }

        if (null == $json)
        {
            return;
        }

        $info->isRelease = (bool)@$json->isRelease ?? false;
        $info->time = (int)@$json->time ?? null;
        $info->previousHash = (string)@$json->previousHash ?? null;
        $info->currentRevisionId = (int)@$json->currentRevisionId ?? null;
        $info->subject = (string)@$json->subject ?? null;
        $info->body = (string)@$json->body ?? null;
        $info->branch = (string)@$json->branch ?? null;
        $info->committerName = (string)@$json->committerName ?? null;
        $info->committerEmail = (string)@$json->committerEmail ?? null;
    }
}