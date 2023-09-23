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

use stdClass;

/**
 * Implements the Rehike debugger context.
 * 
 * This extends stdClass in order to support dynamic properties.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Context extends stdClass
{
    /** 
     * Properties used to build the dialog view.
     */
    public MDialog $dialog;

    /** 
     * Properties used to build the open button's view.
     */
    public MOpenButton $openButton;

    /** 
     * Reports if the debugger is in condensed mode (disabled generally).
     */
    public bool $condensed = false;

    /**
     * Get all tabs in the dialog.
     * 
     * @return MTab[]
     */
    public function getTabs(): array
    {
        return $this->dialog->tabs;
    }

    /**
     * Get all tab IDs available in the debugger.
     * 
     * @return string[]
     */
    public function getTabIds(): array
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
    public function getJsHistoryTabIds(): array
    {
        $out = [];

        foreach ($this->getTabs() as $tab) if ($tab->content->enableJsHistory)
        {
            $out[] = $tab->id;
        }

        return $out;
    }
}