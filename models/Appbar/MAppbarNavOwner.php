<?php
namespace Rehike\Model\Appbar;

class MAppbarNavOwner
{
    public $title;
    public $href;
    public $thumbnail;

    public function __construct($title, $href, $thumbnail)
    {
        $this->title = $title;
        $this->href = $href;
        $this->thumbnail = $thumbnail;
    }
}