<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

class MAccountPickerSettingsButton extends MButton
{
    /**
     * @inheritDoc
     */
    public array $class = [
        "yt-masthead-picker-button",
        "yt-masthead-picker-settings-button"
    ];

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/rehike/config"
                ]
            ]
        ];
        $this->tooltip = $i18n->get("accountPickerSettings");
        $this->icon = (object) [
            "iconType" => "ICON_ACCOUNT_SETTINGS"
        ];
    }
}