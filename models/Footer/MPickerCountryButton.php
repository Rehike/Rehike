<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;

class MPickerCountryButton extends MPickerButton
{
    public $targetId = "yt-picker-country-button";
    public $pickerTarget = "yt-picker-country-footer";
    public $icon;
    public $hasArrow = true;
    
    public function __construct()
    {
        $i18n = i18n::getNamespace("footer");
        
        $label = $i18n->get("pickerLocation");
        $currentCountryName = "COUNTRY NAME";
        
        $this->text = $this->getFormattedLabel($label, $currentCountryName);
        
        $this->attributes["button-action"] = "yt.www.picker.load";
        $this->attributes["button-menu-id"] = "arrow-display";
        $this->attributes["picker-position"] = "footer";
        $this->attributes["picker-key"] = "country";
        $this->attributes["button-toggle"] = "true";
    }
}