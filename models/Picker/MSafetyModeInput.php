<?php
namespace Rehike\Model\Picker;

/**
 * 
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MSafetyModeInput
{
    public string $inputId;
    public string $inputValue;
    public string $text;
    public bool $selected = false;
    
    public function __construct(string $inputId, string $inputValue, string $text)
    {
        $this->inputId = $inputId;
        $this->inputValue = $inputValue;
        $this->text = $text;
    }
    
    public function setSelected(bool $value): void
    {
        $this->selected = $value;
    }
}