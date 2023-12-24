<?php
namespace Rehike\Version;

/**
 * Get version information from the .git folder if it exists
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DotGit
{
    /**
     * Determine if the version system can be used.
     */
    public static function canUse(): bool
    {
        return is_dir(".git");
    }

    /**
     * Get the active Git branch.
     */
    public static function getBranch(): string
    {
        $headFile = @file_get_contents(".git/HEAD");

        if (false == $headFile) return null;

        return trim(str_replace(["ref:", "refs/heads/", " ",], "", $headFile));
    }

    /**
     * Get the latest possible commit hash.
     */
    public static function getCommit(string $branch): string
    {
        $branchFile = @file_get_contents(".git/refs/heads/{$branch}");

        if (false == $branchFile) return null;

        return trim($branchFile);
    }

    /**
     * Return an info array that can be merged with the DotVersion
     * format.
     * 
     * @return string[]
     */
    public static function getInfo(VersionInfo $versionInfo)
    {
        if (!self::canUse()) return []; // Add nothing at all

        if ($branch = self::getBranch())
            $versionInfo->branch = $branch;
        if ($commit = self::getCommit($branch))
            $versionInfo->currentHash = $commit;
    }
}
