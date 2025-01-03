<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the dialog model for the Rehike debugger.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MDialog
{
    /**
     * The dialog's header.
     * 
     * @var MDialogHeader
     */
    public $header;

    /**
     * Stores whether or not the debugger is in condensed mode.
     * 
     * @var bool
     */
    public $condensed;

    /**
     * An array of tabs.
     * 
     * @var MTab[]
     */
    public $tabs = [];

    public function __construct($condensed)
    {
        $this->header = new MDialogHeader($condensed);
    }

    /**
     * Add a tab to the dialog.
     * 
     * @param MTab $tab
     * @return MTabContent Reference to the tab's content.
     */
    public function &addTab($tab)
    {
        $this->tabs[] = $tab;

        return $this->tabs[count($this->tabs) - 1]->content;
    }
}