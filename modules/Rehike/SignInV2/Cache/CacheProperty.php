<?php
namespace Rehike\SignInV2\Cache;

use Attribute;

/**
 * 
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class CacheProperty
{
    public const CACHE_ALWAYS = 0;
    public const CACHE_NEVER = 1;
    
    public function __construct(public int $cacheBehavior = self::CACHE_ALWAYS)
    {
        
    }
}