<?php
namespace Rehike\Model\Channels\Channels4\BrandedPageV2;

class MSubnavMenuButton
{
    public $title;
    public $type;
    public $items;

    public function __construct($type, $title, $array = [])
    {
        $this->type = $type;
        $this->title = $title;

        foreach ($array as $title => $href)
        {
            $this->addMenu(new MSubnavMenuButtonMenu(
                $title, $href
            ));
        }
    }

    public function addMenu($menu)
    {
        $this->items[] = $menu;
    }
}

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