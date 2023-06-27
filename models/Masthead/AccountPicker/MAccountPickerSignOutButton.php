<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n;

class MAccountPickerSignOutButton extends MButton {
    public $class = ["yt-masthead-picker-button"];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this->text = (object) [
            "simpleText" => $i18n->accountPickerSignOut
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/logout"
                ]
            ]
        ];
    }
}