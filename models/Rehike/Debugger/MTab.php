<?php
namespace Rehike\Model\Rehike\Debugger;

/**
 * Implements the tab wrapper. General use should use the
 * createTab method of a MTabContent child.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MTab
{
    public $title;
    public $id = "";
    public $selected = false;
    public $content;

    public function __construct($title, $id, $content, $selected)
    {
        $this->title = $title;
        $this->id = $id;
        $this->content = $content;
        $this->selected = $selected;
    }
}