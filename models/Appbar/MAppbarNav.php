<?php
namespace Rehike\Model\Appbar;

class MAppbarNav
{
    public $items;
    public $owner = null;

    public function addItem($title, $href, $selected)
    {
        $this->items[] = new MAppbarNavItem($title, $href, $selected);
    }

    public function addOwner($title, $href, $thumbnail)
    {
        $this->owner = new MAppbarNavOwner($title, $href, $thumbnail);
    }
}