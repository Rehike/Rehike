<?php
namespace Rehike\Controller\Version;

use Rehike\Version\VersionController;

/**
 * Inlined language system.
 * 
 * This is a preliminary/test system. It may be used universally
 * later.
 */
class i18n
{
    /** @var string[] */
    protected static $data = [];

    /** @var string */
    protected static $language = "";

    /** @var string */
    protected static $defaultLanguage = "en";

    /**
     * Register a language array
     * 
     * @param string $name
     * @param string[] $array of language strings
     * @return void
     */
    public static function register($name, $array)
    {
        self::$data += [$name => &$array];
    }

    public static function getStringId($id)
    {
        return @self::$data[self::$language][$id] 
            ?? @self::$data[self::$defaultLanguage][$id] 
            ?? ""
        ;
    }

    /**
     * Get a string's contents.
     * 
     * @param string $id of the string
     * @param mixed[] $params
     */
    public static function get($id, ...$params)
    {
        $string = self::getStringId($id);

        if (!is_callable($string))
        {
            return sprintf($string, ...$params);
        }
        else
        {
            return $string;
        }
    }

    public static function setLanguage($value)
    {
        self::$language = $value;
    }

    public static function setDefaultLanguage($value)
    {
        self::$defaultLanguage = $value;
    }
}

/**
 * i18n registration
 */
i18n::register("en", [
    "getFormattedDate" => function($date = 0) {
        return date("F j, Y, h:i", $date);
    },
    "brandName" => "Rehike",
    "versionHeader" => "Version %s",
    "nightly" => "Nightly",
    "nightlyInfoTooltip" => "This release is bleeding edge and may contain irregular bugs.",
    "subheaderNightlyInfo" => "Current branch information",
    "nonGitNotice" => "This release of Rehike lacks Git information.",
    "nonGitExtended" => "This may occur if you downloaded the repository directly from GitHub, " .
                        "such as from the \"Download ZIP\" feature. Some version information may be lost or " .
                        "unavailable.",
    "syncGithubButton" => "Synchronize with GitHub",
    "failedNotice" => "Failed to get version information.",
    "remoteFailedNotice" => "Failed to get remote version information.",
    "remoteFailedExtended" => "Version information is limited.",
    "noDotVersionNotice" => "The .version file is missing or corrupted.",
    "noNewVersions" => "No new versions available.",
    "oneNewVersion" => "1 new version available.",
    "varNewVersions" => "%s new versions available.",
    "unknownNewVersions" => "This version is critically out of date.",
    "headingVersionInfo" => "Version information",
    "viewOnGithub" => "View on GitHub"
]);

i18n::setLanguage("en");

/**
 * Model declarations
 */
class MVersionPage
{
    public $headingText;
    public $brandName;
    public $version = ""; // This gets replaced later
    public $nightlyNotice;
    public $nightlyInfo;
    public $failedNotice;
    public $nonGitNotice;

    protected $isNightly = false;

    public function __construct($data)
    {
        $this -> headingText = i18n::get("headingVersionInfo");
        $this -> brandName = i18n::get("brandName");

        if (@$data["semanticVersion"])
        {
            $this->version = i18n::get("versionHeader", $data["semanticVersion"]);
        }

        if (!@$data["isRelease"] && null != $data)
        {
            $this -> nightlyNotice = new MNightlyNotice();
            $this -> nightlyInfo = new MNightlyInfo($data);
            $this -> isNightly = true;
        }

        if (null == $data)
        {
            $this -> failedNotice = new MFailedNotice();
			unset($this->brandName);
			return;
        }

        if (!@$data["supportsDotGit"])
        {
            $this -> nonGitNotice = new MNonGitNotice();
        }
    }
}

class MNightlyNotice
{
    public $text;
    public $tooltip;

    public function __construct()
    {
        $this -> text = i18n::get("nightly");
        $this -> tooltip = i18n::get("nightlyInfoTooltip");
    }
}

class MNightlyInfo
{
    public $headingText;
    public $branch;
    public $commitHash;
    public $fullCommitHash;
    public $isPreviousHash = false;
    public $commitName;
    public $commitDateTime;

    public function __construct(&$data)
    {
        if ($branch = @$data["branch"])
        {
			$this->headingText = i18n::get("subheaderNightlyInfo");
            $this -> branch = $branch;
        }

        if ($hash = @$data["currentHash"])
        {
            $this -> commitHash = self::trimHash($hash);
            $this -> fullCommitHash = $hash;
        }
        else if ($hash = @$data["previousHash"])
        {
            $this -> commitHash = self::trimHash($hash);
            $this -> fullCommitHash = $hash;
            $this -> isPreviousHash = true;
        }

        if ($name = @$data["subject"])
        {
            $this -> commitName = $name;
        }

        if ($time = @$data["time"])
        {
            $this -> commitDateTime = i18n::get("getFormattedDate")($time);
        }

        if (GH_ENABLED && @$this->fullCommitHash)
        {
            $this->ghButton = (object)[];
            $this->ghButton->label = i18n::get("viewOnGithub");
            $this->ghButton->endpoint = "//github.com/" . GH_REPO . "/tree/{$this->fullCommitHash}";
        }
    }

    private static function trimHash($hash)
    {
        return substr($hash, 0, 7);
    }
}

class MNotice
{
    public $text;
    public $description;
}

class MFailedNotice extends MNotice
{
    public function __construct()
    {
        $this -> text = i18n::get("failedNotice");
        $this -> description = i18n::get("noDotVersionNotice");
    }
}

class MNonGitNotice extends MNotice
{
    public function __construct()
    {
        $this -> text = i18n::get("nonGitNotice");
        $this -> description = i18n::get("nonGitExtended");
    }
}

/**
 * Controller declarations
 */
const GH_REPO = "Rehike/Rehike";
const GH_ENABLED = true;

const GH_API_COMMITS = "https://api.github.com/repos/" . GH_REPO . "/commits?sha="; // sha= + branch

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
        if (!GH_ENABLED) return false; // Return false if GH access is not permitted.

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
        $ch = curl_init(GH_API_COMMITS . $branch);
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

class GetVersionController
{
    /**
     * Reference to Rehike\Version\VersionController::$versionInfo
     */
    public static $versionInfo;

    public static function bake()
    {
        self::$versionInfo = &VersionController::$versionInfo;

        $initStatus = VersionController::init();

        if (false == $initStatus)
        {
            return new MVersionPage(null);
        }

        // If remote git is expected, report it
        if (GH_ENABLED) self::$versionInfo += ["expectRemoteGit" => true];

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
return new class {
    public static function get(&$yt, &$template)
    {
        $template = "rehike/version";

        $yt->page = (object)GetVersionController::bake();
    }
};