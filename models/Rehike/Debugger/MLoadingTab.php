<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements a generic "loading" tab for tabs that primarily
 * render with JS.
 * 
 * @author The Rehike Developers
 */
class MLoadingTab extends MTabContent
{
    // This should not update over SPF
    public $enableJsHistory = false;

    public function __construct()
    {
        $this->addLoading();
    }
}