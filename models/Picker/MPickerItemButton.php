<?php
namespace Rehike\Model\Picker;

use Rehike\Model\Common\MButton;

/**
 * Model for picker link buttons.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPickerItemButton
{
    public string|object $title;
    public string $name;
    public string $value;
    public bool $isCurrent = false;
    
    public function __construct(string $title = "", string $name = "", string $value = "", bool $isCurrent = false)
    {
        $this->title = $title;
        $this->name = $name;
        $this->value = $value;
        $this->isCurrent = $isCurrent;
    }
}