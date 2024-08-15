<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;

class MPickerSafetyButton extends MPickerButton
{
    public string $targetId = "yt-picker-safetymode-button";
    public string $pickerTarget = "yt-picker-safetymode-footer";
    public $icon;
    public bool $hasArrow = true;
    
    public function __construct()
    {
        $i18n = i18n::getNamespace("footer");
        
        $label = $i18n->get("pickerSafety");
        $currentCountryName = $i18n->get("safetyOff");
        
        $this->text = $this->getFormattedLabel($label, $currentCountryName);
        
        $this->attributes["button-action"] = "yt.www.picker.load";
        $this->attributes["button-menu-id"] = "arrow-display";
        $this->attributes["picker-position"] = "footer";
        $this->attributes["picker-key"] = "safetymode";
        $this->attributes["button-toggle"] = "true";
    }
}