<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n;
use \Rehike\Model\Common\MButton;

/**
 * Implements the dialog close button.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MDialogHeader
{
    public $title;
    public $closeButton;

    public function __construct($condensed)
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->title = !$condensed ? $i18n->debuggerTitle : $i18n->condensedDebuggerTitle;
        if ($condensed) {
            $this-> helpLink = (object) [
                "text" => $i18n->condensedDebuggerHelpLink,
                "href" => "//github.com/Rehike/Rehike/wiki/Creating-an-issue"
            ];
        }
        $this->closeButton = new MDialogHeaderCloseButton();
    }
}

class MDialogHeaderCloseButton extends MButton
{
    public $style = "STYLE_OPACITY";
    public $icon;

    public $class = [
        "rebug-close-button"
    ];

    public $tooltip;

    public function __construct()
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->tooltip = $i18n->debuggerClose;
        $this->icon = (object) [
            "iconType" => "CLOSE"
        ];
    }
}