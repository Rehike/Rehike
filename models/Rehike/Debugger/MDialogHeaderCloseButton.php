<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n\i18n;
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
    public string $style = "STYLE_OPACITY";

    /**
     * @inheritDoc
     */
    public array $class = [
        "rebug-close-button"
    ];

    public function __construct()
    {
        $i18n = i18n::getNamespace("rehike/debugger");

        $this->tooltip = $i18n->get("debuggerClose");
        $this->icon = (object) [
            "iconType" => "CLOSE"
        ];
    }
}