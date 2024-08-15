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
    /**
     * The type of the current picker.
     * 
     * This is used to report the type to the templater. It must be changed
     * in child classes.
     */
    public string $pickerType = "NONE";
    
    public MPickerHeader $header;
    public array $sections = [];
    
    /**
     * URL to redirect the user to after the form is submitted.
     */
    public string $baseUrl = "/";
    
    public string $formAction = "";
    public string $formMethod = "POST";
    
    public function __construct(string $baseUrl = "/")
    {
        $this->setBaseUrl($baseUrl);
    }
    
    public function setBaseUrl(string $newBaseUrl): void
    {
        $this->baseUrl = $newBaseUrl;
    }
    
    public function addSection(MPickerSection $section): void
    {
        $this->sections[] = $section;
    }
}