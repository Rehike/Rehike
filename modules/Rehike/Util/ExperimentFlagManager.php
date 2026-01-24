<?php
namespace Rehike\Util;

use Rehike\Async\Promise;
use Rehike\FileSystem;
use Rehike\Network;
use RuntimeException;

use function Rehike\Async\async;

/**
 * Manages retrieving and caching the user's experiment flags.
 * 
 * This is currently useful for determining certain flags which must be passed by the player,
 * such as whether their PO tokens are generated per-session or per-content.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class ExperimentFlagManager
{
    /**
     * The cache file to write to.
     */
    public const CACHE_FILE = "experiment_flag_cache.json";
    
    /**
     * Time the cache is valid for.
     */
    public const CACHE_TIME = 60 * 60 * 5; // 5 hours in seconds
    
    /**
     * A cached response valid during the application session.
     * 
     * This is used to avoid a double request if the watch page is the initial route
     * (no visitor data set).
     */
    private static ?object $sessionCachedConfig = null;
    
    private bool $isInitialized = false;
    
    private object $data;
    
    public static function giveYtConfig(object $ytcfg): void
    {
        self::$sessionCachedConfig = $ytcfg;
    }
    
    /**
     * Initialises the manager.
     */
    public function initialize(): Promise
    {
        return async(function()
        {
            if (!$this->loadFromCache())
            {
                yield $this->requestDataFromWeb();
                $this->writeToCache();
            }
            
            $this->isInitialized = true;
        });
    }
    
    /**
     * Gets the entire YouTube configuration object.
     */
    public function getEntireConfig(): object
    {
        if (!$this->isInitialized)
        {
            throw new RuntimeException("Manager is not initialised.");
        }
        
        return $this->data;
    }
    
    /**
     * Gets experiment flag information for the YouTube client (excluding the player).
     */
    public function getExperimentFlags(): object
    {
        if (!$this->isInitialized)
        {
            throw new RuntimeException("Manager is not initialised.");
        }
        
        return $this->data->EXPERIMENT_FLAGS;
    }
    
    /**
     * Gets experiment flag information for the YouTube player.
     */
    public function getWatchPlayerExperimentFlags(): object
    {
        if (!$this->isInitialized)
        {
            throw new RuntimeException("Manager is not initialised.");
        }
        
        $serializedFlags = $this->data->WEB_PLAYER_CONTEXT_CONFIGS->WEB_PLAYER_CONTEXT_CONFIG_ID_KEVLAR_WATCH
            ->serializedExperimentFlags;
        
        $parsedFlags = (object)[];
        $flags = explode("&", $serializedFlags);
        
        foreach ($flags as $flagDef)
        {
            $parts = explode("=", $flagDef);
            $key = $parts[0];
            $value = $parts[1];
            
            $parsedFlags->{$key} = $value;
        }
        
        return $parsedFlags;
    }
    
    private function loadFromCache(): bool
    {
        if (!FileSystem::fileExists($_SERVER["DOCUMENT_ROOT"] . "/cache/" . self::CACHE_FILE))
        {
            return false;
        }
        
        $rawContents = FileSystem::getFileContents($_SERVER["DOCUMENT_ROOT"] . "/cache/" . self::CACHE_FILE);
        
        $json = @json_decode($rawContents);
        if (!$json)
        {
            return false;
        }
        
        if (!isset($json->expire) || $json->expire < time())
        {
            return false;
        }
        
        if (!isset($json->data))
        {
            return false;
        }
        
        $this->data = $json->data;
        return true;
    }
    
    private function writeToCache(): void
    {
        $cacheObj = (object)[];
        
        $cacheObj->expire = time() + self::CACHE_TIME;
        $cacheObj->data = $this->data;
        
        $cacheJsonText = json_encode($cacheObj);
        
        FileSystem::writeFile($_SERVER["DOCUMENT_ROOT"] . "/cache/" . self::CACHE_FILE, $cacheJsonText);
    }

    /**
     * Requests user configuration from InnerTube.
     */
    private function requestDataFromWeb(): Promise
    {
        return async(function()
        {
            if (self::$sessionCachedConfig)
            {
                $this->data = self::$sessionCachedConfig;
                return;
            }
            
            $response = yield Network::urlRequest(
                "https://www.youtube.com",
                [
                    // Force Chrome user agent to ensure we don't get an "Update your browser" message
                    "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36"
                ]
            );

            // Find the configuration set property that contains the visitor
            // data string.
            preg_match("/ytcfg\.set\(({.*?})\);/", $response, $matches);
            $ytcfg = json_decode(@$matches[1]);
            
            $this->data = $ytcfg;
        });
    }
}