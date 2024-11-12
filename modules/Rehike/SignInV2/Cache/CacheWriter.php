<?php
namespace Rehike\SignInV2\Cache;

/**
 * Writes cache objects.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class CacheWriter
{
    private CacheObject $targetObj;
    
    protected function __construct()
    {
        
    }
    
    public static function create(): self
    {
        $me = new self();
        
        $me->targetObj = new CacheObject();
        
        return $me;
    }
    
    public static function fromExistingObject(CacheObject $targetObj)
    {
        $me = new self();
        
        $me->targetObj = $targetObj;
        
        return $me;
    }
    
    public function openKey(string $keyName): CacheWriter
    {
        return self::fromExistingObject($this->targetObj->openKey($keyName));
    }
    
    public function writeKeyValuePair(string $keyName, mixed $value): void
    {
        $this->targetObj->{$keyName} = $value;
    }
    
    public function finish(): CacheObject
    {
        return $this->targetObj;
    }
}