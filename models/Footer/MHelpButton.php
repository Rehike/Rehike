<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Player\Configurable;

class MHelpButton extends MButton {
    public $targetId = "google-help";
    public $class = [
        "yt-uix-button-reverse",
        "yt-google-help-link",
        "inq-no-click"
    ];
    public $attributes = [
        "ghelp-tracking-param" => "",
        "ghelp-anchor" => "google-help",
        "load-chat-support" => "true",
        "feedback-product-id" => "59"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("footer");

        $this->text = (object) [
            "simpleText" => $i18n->get("buttonHelp")
        ];
        $this->icon = (object) [
            "iconType" => "QUESTIONMARK"
        ];
    }
}