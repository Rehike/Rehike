<?php
namespace Rehike\Model\Watch\Watch7;

use Rehike\Model\Common\MButton;

class MCreatorBarEditButton extends MButton
{
    public $style = "STYLE_TEXT_DARK";

    public function __construct($data)
    {
        $this->itemTooltip = $data["tooltip"];
        $this->icon = (object) [
            "iconType" => $data["icon"]
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $data["url"]
                ]
            ]
        ];
        $this->customAttributes = [
            "target" => "_blank"
        ];
    }
}