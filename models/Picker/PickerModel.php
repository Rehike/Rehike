<?php
namespace Rehike\Model\Picker;

/**
 * Base class for all picker models.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PickerModel
{
    public MPickerHeader $header;
    public array $sections = [];
    public string $formAction = "";
    public string $formMethod = "POST";
    
    public function addSection(MPickerSection $section): void
    {
        $this->sections[] = $section;
    }
}