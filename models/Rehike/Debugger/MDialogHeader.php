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

    public function __construct()
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->title = $i18n->debuggerTitle;
        $this->closeButton = new MDialogHeaderCloseButton();
    }
}

class MDialogHeaderCloseButton extends MButton
{
    public $style = "opacity";
    public $hasIcon = true;
    public $icon = "close";

    public $class = [
        "rebug-close-button"
    ];

    public $tooltip;

    public function __construct()
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->tooltip = $i18n->debuggerClose;
    }
}