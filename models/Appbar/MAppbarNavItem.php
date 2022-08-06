<?php
namespace Rehike\Model\Appbar;

use \Rehike\Model\Appbar\MAppbarNavItemStatus;

class MAppbarNavItem
{
    public $title;
    public $href;
    public $status = MAppbarNavItemStatus::Unselected;

    public function __construct($title, $href, $status = MAppbarNavItemStatus::Unselected)
    {
        $this->title = $title;
        $this->href = $href;
        $this->status = $status;
    }
}