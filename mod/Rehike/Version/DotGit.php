<?php
namespace Rehike\Version;

/**
 * Get version information from the .git folder if it exists
 */
class DotGit
{
    public static function canUse()
    {
        return is_dir(".git");
    }

    public static function getBranch()
    {
        $headFile = @file_get_contents(".git/HEAD");

        if (false == $headFile) return null;

        return trim(str_replace(["ref:", "refs/heads/", " ",], "", $headFile));
    }

    public static function getCommit($branch)
    {
        $branchFile = @file_get_contents(".git/refs/heads/{$branch}");

        if (false == $branchFile) return null;

        return trim($branchFile);
    }

    public static function getInfo()
    {
        if (!self::canUse()) return []; // Add nothing at all

        $response = [];

        if ($branch = self::getBranch()) $response += ["branch" => $branch];
        if ($commit = self::getCommit($branch)) $response += ["currentHash" => $commit];

        return $response;
    }
}
