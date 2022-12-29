<?php
namespace Rehike\Model\Channels\Channels4\BrandedPageV2;

class MSubnavMenuButtonMenu
{
    public $title;
    public $href;

    public function __construct($title, $href)
    {
        $this->title = $title;
        $this->href = $href;
    }
}