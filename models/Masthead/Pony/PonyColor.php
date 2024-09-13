<?php
namespace Rehike\Model\Masthead\Pony;

use Attribute;

/**
 * Represents the color associated with a pony.
 * 
 * This is applied as a CSS background to the masthead.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class PonyColor
{
    public int $red;
    public int $green;
    public int $blue;
    
    public function __construct(int $red, int $green, int $blue)
    {
        assert($red >= 0 && $red <= 255);
        assert($green >= 0 && $green <= 255);
        assert($blue >= 0 && $blue <= 255);
        
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }
    
    public function getHexColor(): string
    {
        // These numbers are clamped to 
        $rawHexR = dechex($this->red);
        $rawHexG = dechex($this->green);
        $rawHexB = dechex($this->blue);
        
        $hr = strlen($rawHexR) == 1
            ? "0" . $rawHexR
            : $rawHexR;
        
        $hg = strlen($rawHexG) == 1
            ? "0" . $rawHexG
            : $rawHexG;
            
        $hb = strlen($rawHexB) == 1
            ? "0" . $rawHexB
            : $rawHexB;
        
        return strtoupper($hr . $hg . $hb);
    }
    
    public function getCssColor(): string
    {
        return "#" . strtolower($this->getHexColor());
    }

    /**
     * Determines if the dark logo should be used for this color.
     */
    public function shouldUseDarkLogo(): bool
    {
        return (0.2126 * $this->red + 0.7152 * $this->green + 0.0722 * $this->blue)
            <= 50;
    }
}