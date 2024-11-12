<?php
namespace Rehike\Model\Masthead\Pony;

use ReflectionClass;
use ReflectionClassConstant;

/**
 * API for getting information about a pony.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class Pony extends PonyConstantsBase
{
    protected int $type = self::UNKNOWN_PONY;
    protected PonyColor $color;
    
    public function __construct(int $type)
    {
        $this->type = $type;
        $this->setUpPonyFromType($type);
    }
    
    public function getCssColor(): string
    {
        return $this->color->getCssColor();
    }

    /**
     * Determines if the dark logo should be used for this color.
     */
    public function shouldUseDarkLogo(): bool
    {
        return $this->color->shouldUseDarkLogo();
    }
    
    protected function setUpPonyFromType(int $type): void
    {
        /** @var ?ReflectionClassConstant */
        $reflection = $this->getTypeConstantReflection($type);
        
        if (is_null($reflection))
        {
            // This module isn't important enough to bother with exceptions
            // in, so we just fail silently.
        }
        
        foreach ($reflection->getAttributes(PonyColor::class) as $attribute)
        {
            $this->color = $attribute->newInstance();
        }
        
        if (!isset($this->color))
        {
            // If we don't have any colour set (i.e. there is no colour attribute
            // on the constant), then we'll default to white to prevent a crash.
            $this->color = new PonyColor(255, 255, 255);
        }
    }
    
    protected function getTypeConstantReflection(int $type): ?ReflectionClassConstant
    {
        $reflection = new ReflectionClass(self::class);
        
        foreach ($reflection->getReflectionConstants() as $constant)
        {
            /** @var ReflectionClassConstant */
            $constant;
            
            if ($constant->getValue() == $type)
            {
                return $constant;
            }
        }
        
        return null;
    }
}