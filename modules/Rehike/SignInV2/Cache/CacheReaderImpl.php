<?php
namespace Rehike\SignInV2\Cache;

/**
 * Implements the internal parts of the cache reader object.
 * 
 * A separate implementation class is used to ensure that the private accessor
 * names do not conflict with properties on the underlying object. For example,
 * we don't want "parent" or "currentObj" to access the names of the properties
 * on this object.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CacheReaderImpl
{
    private ?CacheReader $parent = null;
    private object $currentObj;
    
    public function __construct(object $objToBeWrapped, ?CacheReader $parent = null)
    {
        $this->currentObj = $objToBeWrapped;
        $this->parent = $parent;
    }
    
    /**
     * Get the parent object of the current object, if it exists.
     */
    public function getParent(): ?CacheReader
    {
        return $this->parent;
    }
    
    /**
     * Unwraps the cache reader and retrieves the underlying object.
     * 
     * @internal
     */
    public function unwrapObject(): object
    {
        return $this->currentObj;
    }
    
    public function __get(string $key): mixed
    {
        $value = $this->currentObj->{$key};
        
        if (is_object($value))
        {
            return new CacheReader($value, $this);
        }
        
        return $value;
    }
}