<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n;
use \Rehike\Model\Common\MButton;

/**
 * Implements the dialog header.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MDialogHeader
{
    /**
     * Title of the header.
     * 
     * @var string
     */
    public $title;

    /**
     * A button to close the dialog.
     * 
     * @var MDialogHeaderCloseButton
     */
    public $closeButton;

    /**
     * Help link for condensed mode.
     * 
     * @var object
     */
    public $helpLink;

    /**
     * Create a new dialog header.
     * 
     * @param bool $condensed Is the debugger condensed?
     * @parma bool $closeButton Whether or not to include the close button.
     */
    public function __construct($condensed, $closeButton = true)
    {
        $i18n = &i18n::getNamespace("rebug");

        $this->title = !$condensed ? $i18n->debuggerTitle : $i18n->condensedDebuggerTitle;

        if ($condensed) {
            $this-> helpLink = (object) [
                "text" => $i18n->condensedDebuggerHelpLink,
                "href" => "//github.com/Rehike/Rehike/wiki/Creating-an-issue"
            ];
        }

        if (!$condensed)
        {
            $this->historyButton = new MHistoryButton();
        }

        if ($closeButton)
        {
            $this->closeButton = new MDialogHeaderCloseButton();
        }
    }
}

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