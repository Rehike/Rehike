<?php
namespace Rehike\Model\Footer;

use Rehike\i18n;
use Rehike\Model\Common\MButton;
use Rehike\ConfigManager\ConfigManager;
use Rehike\Player\Configurable;

class MFooterLink {
    /** @var string */
    public $simpleText;

    /** @var object */
    public $navigationEndpoint;

    public function __construct($text, $url) {
        $this->simpleText = $text;
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $url
                ]
            ]
        ];
    }
}