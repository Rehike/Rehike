<?php
namespace Rehike\Version;

use const Rehike\Constants\GH_ENABLED;
use const Rehike\Constants\GH_REPO;

/**
 * Pulls the latest Git state from the remote repository.
 * 
 * Each successful request shall be cached for the next 60 minutes. This is
 * to avoid hitting the request cap for unauthenticated GitHub rest API requests.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RemoteGit
{
    public const GH_API_COMMITS = "https://api.github.com/repos/" . GH_REPO . "/commits?sha="; // sha= + branch

    /**
     * Attempt to retrieve remote GitHub information, either
     * through the GitHub API or a cached earlier download.
     */
    public static function getInfo(string $branch): mixed
    {
        // Return false if GH access is not permitted.
        if (!GH_ENABLED) return false;

        return self::useCache($branch) ?? self::useRemote($branch) ?? false;
    }

    private static function useCache(string $branch): mixed
    {
        $response = null;

        $lockFile = @file_get_contents("cache/remote-git.lock");
        $lockJson = @json_decode($lockFile);

        // Validate the lock file
        if ($lockFile && null != $lockJson && ($branch = @$lockJson->{$branch}))
        {
            // Check if the cache has expired
            if (@$branch->time > time() && @$branch->file)
            {
                // Attempt to get the get the cache file
                $cacheFile = @file_get_contents($branch->file);
                $cacheJson = @json_decode($cacheFile);
				
                // Validate the cache and return it if it's valid
                if ($cacheFile && null != $cacheJson)
                {
                    return $cacheJson;
                }
            }
        }

        return $response;
    }

    /**
     * Store a cache file and reuse it later
     * 
     * TODO: Unified cache system for all Rehike components.
     * 
     * @param string $branch
     * @param string $encodedJson
     * @return bool status (false on failure, true on success)
     */
    private static function storeCache(string $branch, string $encodedJson): mixed
    {
        $newLockContents = (object)[];
        $filename = "remote-git-{$branch}.json";

        // Grab the lock file contents (if they exist)
        $lockFile = @file_get_contents("cache/remote-git.lock");
        $lockJson = @json_decode($lockFile);

        if ($lockFile && null != $lockJson)
        {
            $newLockContents = &$lockJson;
        }

        // Store the encoded json in a file corresponding to the
        // specific branch.
        if (!is_dir("cache")) mkdir("cache");

        $fileFailure = false;

        $fh = @fopen("cache/{$filename}", "w") ?? ($fileFailure = true);
        @fwrite($fh, $encodedJson) ?? ($fileFailure = true);
        @fclose($fh) ?? ($fileFailure = true);

        // Update the lock file
        $newLockContents->{$branch} = (object)[
            "time" => time() + 60 * 60,
            "file" => "cache/{$filename}"
        ];

        $fh = @fopen("cache/remote-git.lock", "w") ?? ($fileFailure = true);
        @fwrite($fh, json_encode($newLockContents)) ?? ($fileFailure = true);
        @fclose($fh) ?? ($fileFailure = true);

        return !$fileFailure;
    }

    private static function useRemote(string $branch): mixed
    {
        $ch = curl_init(self::GH_API_COMMITS . $branch);
        curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:99.0) Gecko/20100101 Firefox/99.0"
		]);

        // Attempt to request the GitHub API
        $data = @curl_exec($ch);

        // Validate the response (if cURL failed, it will be false)
        if (!$data) return null;
        if (200 != curl_getinfo($ch, CURLINFO_RESPONSE_CODE)) return null;

        // If the response is JSON, attempt to decode it
        $json = @json_decode($data);
        if (null == $json) return null;

        // Attempt to write a cache file.
        self::storeCache($branch, $data) 
            || trigger_error("Failed to cache Git result.", E_USER_WARNING);
        
        // Return the remote object.
        return $json;
    }
}