<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\Model\Common\MButton;
use Rehike\i18n;

class MAccountPickerAddButton extends MButton {
    public $class = ["yt-masthead-picker-button"];

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $this->text = (object) [
            "simpleText" => $i18n->accountPickerAddAccount
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "//accounts.google.com/AddSession?passive=false&hl=en&continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fhl%3Den%26next%3D%252F%253Fdisable_polymer%253D1%26action_handle_signin%3Dtrue%26app%3Ddesktop&uilel=0&service=youtube"
                ]
            ]
        ];
    }
}