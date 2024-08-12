<?php
namespace Rehike\Model\Masthead\Pony;

use Attribute;

/**
 * Stores the name(s) of a pony.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE)]
class PonyName
{
    public const FULL_NAME = 0;
    public const SHORT_NAME = 1;
    public const VARIANT_NAME = 2;
    
    public string $value;
    
    public function __construct(string $name, int $type = self::FULL_NAME)
    {
        $this->value = $name;
    }
}