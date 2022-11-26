<?php
namespace Rehike\Debugger;

use \Rehike\Model\Rehike\Debugger\{
    MOpenButton,
    MDialog,
    MTab,
    MErrorTab,
    MYtWalker,
    MLoadingTab
};

/**
 * Implements the Rehike debugger context.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Context
{
    /** 
     * Properties used to build the dialog view.
     * 
     * @var MDialog 
     */
    public $dialog = null;

    /** 
     * Properties used to build the open button's view.
     * 
     * @var MOpenButton 
     */
    public $openButton = null;

    /** 
     * Reports if the debugger is in condensed mode (disabled generally).
     * 
     * @var bool 
     */
    public $condensed = false;

    /**
     * Get all tabs in the dialog.
     * 
     * @return MTab[]
     */
    public function getTabs()
    {
        return $this->dialog->tabs;
    }

    /**
     * Get all tab IDs available in the debugger.
     * 
     * @return string[]
     */
    public function getTabIds()
    {
        $out = [];

        foreach ($this->getTabs() as $tab)
        {
            $out[] = $tab->id;
        }

        return $out;
    }

    /**
     * Get the tab IDs to be affected by the history manager in the JS land.
     * 
     * @return string[]
     */
    public function getJsHistoryTabIds()
    {
        $out = [];

        foreach ($this->getTabs() as $tab) if ($tab->content->enableJsHistory)
        {
            $out[] = $tab->id;
        }

        return $out;
    }
}