<?php
namespace Rehike\SignInV2\Cache;

/**
 * Interface for objects which can be cached.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface ICacheable
{
    /**
     * Restores the object properties from an abstract cache object.
     */
    public function restoreFromCache(CacheReader $cache): void;
    
    public function exportCacheObject(): CacheObject;
}