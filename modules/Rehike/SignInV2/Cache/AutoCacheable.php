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
 * @author Isabella Lulamoon <kawapure@gmail.com>
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
    
    public function exportCacheObject(): CacheObject
    {
        $writerRoot = new CacheWriter();
        
        $writerRoot->writeKeyValuePair("type", static::class);
        
        $propsWriter = $writerRoot->openKey("props");
        $reflection = new ReflectionObject($this);
        
        foreach ($reflection->getProperties() as $property)
        {
            $propertyCacheProperties = $property->getAttributes(
                CacheProperty::class
            );
            
            if (
                $propertyCacheProperties[0]->newInstance()->cacheBehavior ==
                CacheProperty::CACHE_NEVER
            )
            {
                continue;
            }
            
            if ($property->isProtected() || $property->isPrivate())
            {
                $property->setAccessible(true);
            }
            
            $propsWriter->writeKeyValuePair(
                $property->getName(), $property->getValue()
            );
        }
        
        return $writerRoot->finish();
    }
}