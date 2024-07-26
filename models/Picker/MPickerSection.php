<?php
namespace Rehike\Model\Picker;

/**
 * Model for a picker section.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MPickerSection
{
    public array $items = [];
    
    public function addItem(object $item): void
    {
        $this->items[] = $item;
    }
}