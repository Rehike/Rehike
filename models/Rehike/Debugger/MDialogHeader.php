<?php
namespace Rehike\Model\Rehike\Debugger;

use \Rehike\i18n\i18n;

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
     * History button (unused)
     * 
     * @var MHistoryButton
     */
    public $historyButton;

    /**
     * Create a new dialog header.
     * 
     * @param bool $condensed Is the debugger condensed?
     * @parma bool $closeButton Whether or not to include the close button.
     */
    public function __construct($condensed, $closeButton = true)
    {
        $i18n = i18n::getNamespace("rehike/debugger");

        $this->title = !$condensed
            ? $i18n->get("debuggerTitle")
            : $i18n->get("condensedDebuggerTitle");

        if ($condensed)
        {
            $this->helpLink = (object) [
                "text" => $i18n->get("condensedDebuggerHelpLink"),
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