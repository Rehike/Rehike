<?php
namespace Rehike\SignInV2\Cache;

use stdClass;

/**
 * Represents a cache object.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CacheObject extends stdClass
{
    public function openKey(string $keyName): CacheObject
    {
        if (!isset($this->{$keyName}))
        {
            $this->{$keyName} = new CacheObject();
        }
        
        return $this->{$keyName};
    }
}