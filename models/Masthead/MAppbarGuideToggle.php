<?php
namespace Rehike\Model\Masthead;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

class MAppbarGuideToggle extends MButton
{
    public string $style = "STYLE_TEXT";
    public string $size = "SIZE_DEFAULT";
    public string $targetId = "appbar-guide-button";

    /**
     * @inheritDoc
     */
    public array $class = [
        "appbar-guide-toggle",
        "appbar-guide-clickable-ancestor"
    ];

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "controls" => "appbar-guide-menu",
                "label" => $i18n->get("appbarGuideLabel")
            ]
        ];

        $this->icon = (object) [
            "iconType" => "APPBAR_GUIDE"
        ];
    }
}