<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;

class MPickerLanguageButton extends MPickerButton
{
    public string $targetId = "yt-picker-language-button";
    public string $pickerTarget = "yt-picker-language-footer";
    public bool $hasArrow = true;
    
    public function __construct()
    {
        $i18n = i18n::getNamespace("footer");
        $culture = i18n::getNamespace("_culture");
        $cultureStrings = $culture->getAllTemplates();
        
        $label = $i18n->get("pickerLanguage");
        $currentLanguageName = isset($cultureStrings->expandedLanguageName)
            ? $cultureStrings->expandedLanguageName
            : $cultureStrings->languageName;
        
        $this->text = $this->getFormattedLabel($label, $currentLanguageName);
        $this->icon = (object)[
            "iconType" => "FOOTER_LANGUAGE"
        ];
        
        $this->attributes["button-action"] = "yt.www.picker.load";
        $this->attributes["button-menu-id"] = "arrow-display";
        $this->attributes["picker-position"] = "footer";
        $this->attributes["picker-key"] = "language";
        $this->attributes["button-toggle"] = "true";
    }
}