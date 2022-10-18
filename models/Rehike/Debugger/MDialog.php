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
    public $header;
    public $condensed;
    public $tabs = [];

    public function __construct($condensed)
    {
        $this->header = new MDialogHeader($condensed);
    }

    public function &addTab($tab)
    {
        $this->tabs[] = $tab;

        return $this->tabs[count($this->tabs) - 1]->content;
    }
}