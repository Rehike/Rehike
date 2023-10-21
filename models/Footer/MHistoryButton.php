<?php
namespace Rehike\Model\Footer;

use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Player\Configurable;

class MHistoryButton extends MButton {
    public $class = [
        "footer-history"
    ];

    public function __construct() {
        $i18n = i18n::getNamespace("footer");

        $this->icon = (object) [
            "iconType" => "FOOTER_HISTORY"
        ];
        $this->text = (object) [
            "simpleText" => $i18n->get("buttonHistory")
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/feed/history"
                ]
            ]
        ];
    }
}