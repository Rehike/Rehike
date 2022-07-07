<?php
namespace Rehike\Model\Appbar;

class MAppbar
{
    public $nav;

    public function __construct() {}

    public function addNav()
    {
        $this->nav = new MAppbarNav();
    }
}