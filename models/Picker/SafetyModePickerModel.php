<?php
namespace Rehike\Model\Picker;

use Rehike\i18n\i18n;
use Rehike\i18n\RehikeLocale;
use Rehike\Model\Common\MButton;
use Rehike\Util\ParsingUtils;
use Rehike\FormattedString;
use Rehike\Util\FormattedStringBuilder;
use Rehike\Util\FormattedStringBuilder\PrintfTemplateBuilderParams;

/**
 * Used for the restricted mode picker.
 * 
 * Restricted mode is known internally as "safety mode", which is the
 * terminology which carried over to Rehike.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class SafetyModePickerModel extends PickerModel
{
    public string $pickerType = "SAFETY_MODE";
    
    public array $safetyModeDescription = [];
    public FormattedString $lockedSectionText;
    
    public MSafetyModeInput $onButton;
    public MSafetyModeInput $offButton;
    public MButton $saveButton;
    
    public function __construct(object $data, string $baseUrl)
    {
        parent::__construct($baseUrl);
        $this->header = new MPickerHeader();
        $this->header->closeButtonTargetId = "yt-picker-safetymode-button";
        $this->formAction = "/set_safety_mode";
        
        $i18n = i18n::getNamespace("picker");
        
        $this->header->titleText = $i18n->get("safetyModeTitle");
        
        $this->safetyModeDescription[] = $i18n->get("safetyModeSubtitle");
        $this->safetyModeDescription[] = $i18n->get("safetyModeSubtitle2");
        
        $formattedStringBuilder = new FormattedStringBuilder();
        $aaa = $formattedStringBuilder->parseFromPrintfTemplates(
            new PrintfTemplateBuilderParams(
                $i18n->get("safetyModeLockSectionTemplate")
            ),
            new PrintfTemplateBuilderParams(
                $i18n->get("safetyModeLockSectionSignIn"),
                FormattedStringBuilder::RUN_AS_LINK,
                "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Faction_handle_signin%3Dtrue%26app%3Ddesktop%26hl%3Den%26next%3D%252F%253Fsafety_mode_redirect%253D1&hl=en&passive=true&service=youtube&uilel=3"
            )
        );
        
        $this->lockedSectionText = $aaa->build();
        
        $this->bakeButtons();
    }
    
    /**
     * Bakes the "on" and "off" buttons.
     */
    private function bakeButtons(): void
    {
        $i18n = i18n::getNamespace("footer");
        $i18nMisc = i18n::getNamespace("misc");
        
        $this->onButton = new MSafetyModeInput(
            "on",
            "true", // Enable safety mode
            $i18n->get("safetyOn")
        );
        
        $this->offButton = new MSafetyModeInput(
            "off",
            "false", // Enable safety mode
            $i18n->get("safetyOff")
        );
        
        $this->offButton->setSelected(true);
        
        $this->saveButton = new MButton([
            "text" => (object)[
                "runs" => [
                    (object)[
                        "text" => $i18nMisc->get("btnSave")
                    ]
                ]
            ],
            "customAttributes" => [
                "type" => "submit",
                "onclick" => ";return true;"
            ]        
        ]);
    }
}