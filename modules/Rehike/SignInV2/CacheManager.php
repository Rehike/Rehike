<?php
declare(strict_types=1);
namespace Rehike\SignInV2;

use Rehike\FileSystem;
use Rehike\SignInV2\CacheNew\SigninCache;

/**
 * Time constants, relative to time() output which is
 * UNIX timestamp in seconds.
 */
const SECONDS = 1;
const MINUTES = 60 * SECONDS;
const HOURS = 60 * MINUTES;
const DAYS = 24 * HOURS;
const WEEKS = 7 * DAYS;

/**
 * Manages cached data for the acceleration of the sign in system.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CacheManager
{
    private const CACHE_FILE = "cache/signin2_cache.json";
    private const CACHE_VERSION = 3;
    
    public static function getCache(): ?SigninCache
    {
        if (FileSystem::fileExists(self::CACHE_FILE))
        {
            try
            {
                $contents = json_decode(FileSystem::getFileContents(self::CACHE_FILE));
                
                if (!isset($contents->version) || $contents->version != self::CACHE_VERSION)
                    return null;
                
                if (!isset($contents->expire) || time() > $contents->expire)
                    return null;
                
                if (!isset($contents->sessionId) || 
                    $contents->sessionId != GaiaAuthManager::getUniqueSessionCookie())
                {
                    return null;
                }
                
                if (!isset($contents->switcherResponse))
                    return null;
                
                return new SigninCache(
                    version: $contents->version,
                    expire: $contents->expire,
                    sessionId: $contents->sessionId,
                    switcherResponse: $contents->switcherResponse,
                    currentUcid: $contents->currentUcid,
                );
            }
            catch (\Throwable $e)
            {
                return null;
            }
        }
        
        return null;
    }
    
    public static function writeCache(object $switcherResponse, ?string $currentUcid): void
    {
        $session = GaiaAuthManager::getUniqueSessionCookie();
        
        $data = (object)[
            "expire" => time() + (2 * DAYS),
            "version" => self::CACHE_VERSION,
            "sessionId" => $session,
            "switcherResponse" => $switcherResponse,
            "currentUcid" => $currentUcid,
        ];
        
        FileSystem::writeFile(self::CACHE_FILE, json_encode($data));
    }
}