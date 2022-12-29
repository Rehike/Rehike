<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n;
use \Rehike\Model\Common\MButton;

/**
 * Implements the close button used in the header. This is used in the JS
 * world to close the dialog.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
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