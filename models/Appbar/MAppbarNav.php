<?php
namespace Rehike\Model\Appbar;

class MAppbarNav
{
    public $items;
    public $owner = null;

    public function addItem($title, $href, $status)
    {
        $this->items[] = new MAppbarNavItem($title, $href, $status);
    }

    public function addOwner($title, $href, $thumbnail)
    {
        $this->owner = new MAppbarNavOwner($title, $href, $thumbnail);
    }
}