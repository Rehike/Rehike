<?php
namespace Rehike\Controller\Version;

use Rehike\Controller\core\HitchhikerController;
use Rehike\Version\VersionController;
use Rehike\i18n;

use Rehike\Model\Rehike\Version\MVersionPage;

/**
 * Use remote Git repo if possible.
 * 
 * Each successful request shall be cached for the next 60 minutes. This is
 * to avoid hitting the request cap for unauthenticated GitHub rest API requests.
 */
class RemoteGit
{
    /**
     * Attempt to retrieve remote GitHub information, either
     * through the GitHub API or a cached earlier download.
     * 
     * @param string $branch
     * @return object|false
     */
    public static function getInfo($branch)
    {
        if (!GetVersionController::GH_ENABLED) return false; // Return false if GH access is not permitted.

        return self::useCache($branch) ?? self::useRemote($branch) ?? false;
    }

    /**
     * @return object|null
     */
    private static function useCache($branch)
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
     * @param string $branch
     * @param string $encodedJson
     * @return bool status (false on failure, true on success)
     */
    private static function storeCache($branch, $encodedJson)
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

    /**
     * @return object|null
     */
    private static function useRemote($branch)
    {
        $ch = curl_init(GetVersionController::GH_API_COMMITS . $branch);
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

class GetVersionController extends HitchhikerController
{
    public const GH_REPO = "Rehike/Rehike";
    public const GH_ENABLED = true;

    public const GH_API_COMMITS = "https://api.github.com/repos/" . self::GH_REPO . "/commits?sha="; // sha= + branch

    /**
     * Reference to Rehike\Version\VersionController::$versionInfo
     */
    public static $versionInfo;

    public $template = "rehike/version";

    public function onGet(&$yt, $request)
    {
        i18n::newNamespace("rehike/version")->registerFromFolder("i18n/rehike/version");
        
        $yt->page = (object)self::bake();
    }

    public static function bake()
    {
        self::$versionInfo = &VersionController::$versionInfo;

        $initStatus = VersionController::init();

        if (false == $initStatus)
        {
            return new MVersionPage(null);
        }

        // If remote git is expected, report it
        if (self::GH_ENABLED) self::$versionInfo += ["expectRemoteGit" => true];

        // ...and attempt to use it
        if (
            @self::$versionInfo["branch"] && 
            ( $rg = RemoteGit::getInfo(self::$versionInfo["branch"]) )
        )
        {
            self::$versionInfo += ["remoteGit" => $rg];

            // If the previous commit is reported, but not the current commit,
            // attempt to retrieve the current commit hash from git.
            if (@self::$versionInfo["previousHash"] && !@self::$versionInfo["currentHash"])
            for ($i = 1, $l = count($rg); $i < $l; $i++)
            {
                $currentItem = &$rg[$i];
                $previousItem = &$rg[$i - 1];

                if (self::$versionInfo["previousHash"] == @$currentItem->sha)
                {
                    // Previous item must be the current hash
                    // so use its hash
                    self::$versionInfo += ["currentHash" => $previousItem->sha];
                }
            }
        }

        self::$versionInfo += ["semanticVersion" => VersionController::getVersion()];

        // Return a version page model.
        return new MVersionPage(self::$versionInfo);
    }
}

// export
return new GetVersionController();