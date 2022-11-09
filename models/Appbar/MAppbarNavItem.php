<?php
namespace Rehike\Model\Appbar;

class MAppbarNavItem
{
    public $title;
    public $href;
    public $status = self::StatusUnselected;

    const StatusUnselected = 0;
    const StatusPartiallySelected = 1;
    const StatusSelected = 2;

    public function __construct($title, $href, $status = self::StatusUnselected)
    {
        $this->title = $title;
        $this->href = $href;
        $this->status = $status;
    }
}