<?php
namespace Rehike\Model\Comments;

use Rehike\Boot\ShutdownEvents;
use Rehike\FileSystem;

/**
 * Manages display name caching.
 * 
 * This is used to lessen the load on the Data API approach (or if we implement
 * a pure-InnerTube approach at some point in the future). Since it's likely
 * that a user will see repeated usernames when browsing comment sections, it's
 * convenient to cache common display names.
 * 
 * This cache system is biased towards the most common names encountered by a
 * user, so less common names will be pruned away as the user encounters more
 * names.
 * 
 * @static
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class DisplayNameCacheManager
{
    public const CACHE_PATH = "cache/ucid_display_name_cache.json";
    
    /**
     * The maximum number of items to cache.
     */
    public const MAX_ITEMS = 150;
    
    /**
     * The amount of items to additionally remove if we exceed the maximum
     * number of items, in order to ensure a buffer.
     */
    public const PRUNE_OVERDO_AMOUNT = 25;
    
    /**
     * Common time for updated entries during the current session.
     * 
     * Basically, there's no reason to ever call into time() a bunch because
     * everything going to have just about the same result anyway. It's a lot
     * of overhead for no good result, because what we're really interested in
     * differentiating between are actual user sessions.
     * 
     * Thus, we just call time() once to generate a mostly-unique session ID
     * and reuse the same value.
     */
    private static int $commonTime = 0;
    
    private static ?object $items = null;
    
    public static function __initStatic(): void
    {
        self::$items = self::loadJson() ?? (object)[];
        self::$commonTime = time();
        
        ShutdownEvents::register(function() {
            self::prune();
            self::commitToDisk();
        }, name: static::class . "-commit-to-disk");
    }
    
    /**
     * Insert a cache item or update the existing item.
     */
    public static function insert(string $ucid, string $displayName): void
    {
        if (!isset(self::$items->{$ucid}))
        {
            self::$items->{$ucid} = (object)[];
            self::insert($ucid, $displayName);
            return;
        }
        
        self::$items->{$ucid}->displayName = $displayName;
        self::$items->{$ucid}->lastModifiedTime = self::$commonTime;
    }
    
    /**
     * Gets an item from the cache.
     */
    public static function get(string $ucid): string
    {
        return self::$items->{$ucid}->displayName;
    }
    
    /**
     * Checks if the cache manager has the specified UCID.
     */
    public static function has(string $ucid): bool
    {
        return isset(self::$items->{$ucid});
    }
    
    /**
     * Prune the excess of items.
     */
    public static function prune(): void
    {
        if (count((array)self::$items) <= self::MAX_ITEMS)
        {
            // If we aren't already full, then we simply don't need to run the
            // algorithm.
            return;
        }
        
        $lmtMap = [];
        
        foreach (self::$items as $key => &$value)
        {
            $lmtMap[$value->lastModifiedTime] = $key;
        }
        
        // Sort the keys of the last-modified-time map to make sure the earliest
        // entries were last modified the longest time ago.
        ksort($lmtMap, SORT_NUMERIC);
        
        while (count($lmtMap) >= self::MAX_ITEMS - self::PRUNE_OVERDO_AMOUNT)
        {
            unset(self::$items->{$lmtMap[0]});
        }
    }
    
    /**
     * Loads the cache JSON from disk.
     */
    private static function loadJson(): ?object
    {
        if (FileSystem::fileExists(self::CACHE_PATH))
        {
            try
            {
                return @json_decode(FileSystem::getFileContents(self::CACHE_PATH));
            }
            catch (\Exception $e)
            {
                return null;
            }
        }
        
        return null;
    }
    
    /**
     * Commits the cache to disk.
     */
    private static function commitToDisk(): void
    {
        FileSystem::writeFile(self::CACHE_PATH, json_encode(self::$items));
    }
}