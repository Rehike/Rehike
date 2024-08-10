<?php
namespace Rehike\SignInV2\Cache;

use ReflectionObject;

/**
 * Implements automatic cacheability.
 * 
 * This trait effectively implements ICacheable, however, since PHP traits
 * cannot implement interfaces, all users must manually specify that they
 * implement this interface instead.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
trait AutoCacheable
{
    public function restoreFromCache(CacheReader $cache): void
    {
        foreach ($cache->properties as $key => $value)
        {
            $this->{$key} = $value;
        }
    }
    
    public function exportCacheObject()
    {
        $writer = new CacheWriter();
        $props = $writer->openKey("props");
        $reflection = new ReflectionObject($this);
        
        foreach ($reflection->getProperties() as $property)
        {
            
        }
    }
}