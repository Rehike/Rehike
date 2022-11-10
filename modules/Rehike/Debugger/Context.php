<?php
namespace Rehike\Debugger;

use \Rehike\Model\Rehike\Debugger\{
    MOpenButton,
    MDialog,
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
    /** @var MDialog */
    public $dialog = null;

    /** @var MOpenButton */
    public $openButton = null;

    /** @var bool */
    public $condensed = false;

    public function getTabs()
    {
        return $this->dialog->tabs;
    }

    public function getTabIds()
    {
        $out = [];

        foreach ($this->getTabs() as $tab)
        {
            $out[] = $tab->id;
        }

        return $out;
    }

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