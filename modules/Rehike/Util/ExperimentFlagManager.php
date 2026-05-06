<?php
namespace Rehike\Util;

use Rehike\Async\Promise;
use Rehike\FileSystem;
use Rehike\Logging\DebugLogger;
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
                    "userAgent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36"
                ]
            );

            // Find the configuration set property that contains the visitor
            // data string.
            $ytcfg = (object)[];
            
            preg_match_all("/ytcfg\.set\(({.*?})\);/", $response, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            for ($i = 0; $i < count($matches); $i++)
            {
                $index = $matches[$i][1][1];
                try
                {
                    $result = $this->extractJson($response, $index);
                    foreach ($result as $key => $value)
                    {
                        $ytcfg->{$key} = $value;
                    }
                }
                catch (\Throwable $e)
                {
                    \Rehike\Logging\DebugLogger::print(
                        "Failed to extract state from ytcfg object #%d in the response: %s", 
                        $i, (string)$e
                    );
                }
            }
            
            $this->data = $ytcfg;
        });
    }
    
    /**
     * Extracts a JSON string from the document string and parses it into a PHP object.
     * 
     * @see https://github.com/Leymonaide/Retwitter/blob/49a84fbabec7fb69ced9c87350a779fe4cd79b8f/modules/Retwitter/SignIn/AuthManager.php#L107-L218
     * 
     * @param string $rawDoc  The original document.
     * @param int $index      The index of the JSON object in the document.
     */
    private function extractJson(
        string $rawDoc,
        int $index,
    ): ?object
    {
        $docLength = strlen($rawDoc);
        
        // Private working variables for JSON extractor:
        $braceCounter = 0;
        $parsingString = false;
        $stringTerminator = "";
        
        // Output variables of JSON extractor:
        $jsonBegin = 0;
        $jsonEnd = 0;
        
        // JSON extractor:
        while ($index < $docLength)
        {
            $char = $rawDoc[$index];
            
            if (($char == '{') && !$parsingString)
            {
                if ($braceCounter == 0)
                {
                    $jsonBegin = $index;
                }
                $braceCounter++;
            }
            else if (($char == '}') && !$parsingString)
            {
                $braceCounter--;
                if ($braceCounter == 0)
                {
                    // End of JSON object.
                    $jsonEnd = $index;
                    break;
                }
            }
            else if (($char == '"' || $char == '\'') && !$parsingString)
            {
                $parsingString = true;
                $stringTerminator = $char;
            }
            else if (($char == $stringTerminator) && $parsingString)
            {
                if ($rawDoc[$index - 1] != '\\')
                {
                    $parsingString = false;
                }
            }
            
            $index++;
        }
        
        $length = $jsonEnd - $jsonBegin + 1;
        $jsonStr = substr($rawDoc, $jsonBegin, $length);
        
        return json_decode($jsonStr, flags: JSON_THROW_ON_ERROR);
    }
}