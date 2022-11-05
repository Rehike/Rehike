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

    public static function fromData($data) {
        $items = [];

        foreach ($data as $item) {
            if ($item -> selected) {
                $title = $item -> title;
            } else {
                $items[$item -> title] = $item -> endpoint -> commandMetadata -> webCommandMetadata -> url;
            }
        }

        return new self("view", $title, $items);
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