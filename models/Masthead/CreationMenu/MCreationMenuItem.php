<?php
namespace Rehike\Model\Masthead\CreationMenu;

use Rehike\Model\Common\MButton;

class MCreationMenuItem extends MButton {
    public function __construct($type, $label, $url) {
        $this->targetId = "creation-$type-menu-item";
        $this->type = $type;
        $this->icon = (object) [
            "iconType" => "CREATION_" . strtoupper($type)
        ];
        $this->text = (object) [
            "simpleText" => $label
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $url
                ]
            ]
        ];
    }
}