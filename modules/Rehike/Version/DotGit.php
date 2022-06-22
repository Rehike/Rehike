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
     * 
     * @return bool
     */
    public static function canUse()
    {
        return is_dir(".git");
    }

    /**
     * Get the active Git branch.
     * 
     * @return string
     */
    public static function getBranch()
    {
        $headFile = @file_get_contents(".git/HEAD");

        if (false == $headFile) return null;

        return trim(str_replace(["ref:", "refs/heads/", " ",], "", $headFile));
    }

    /**
     * Get the latest possible commit hash.
     * 
     * @return string
     */
    public static function getCommit($branch)
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
    public static function getInfo()
    {
        if (!self::canUse()) return []; // Add nothing at all

        $response = [];

        if ($branch = self::getBranch()) $response += ["branch" => $branch];
        if ($commit = self::getCommit($branch)) $response += ["currentHash" => $commit];

        return $response;
    }
}
