<?php
namespace Rehike\Model\Appbar;

class MAppbarNavItem
{
    public $title;
    public $href;
    public $selected = false;

    public function __construct($title, $href, $selected = false)
    {
        $this->title = $title;
        $this->href = $href;
        $this->selected = $selected;
    }
}