<?php
namespace Rehike\Model\Common\Menu;

class MMenu {
    /** @var MMenuItem[] */
    public $items = [];

    /** @var string[] */
    public $containerClass = [];

    /** @var string */
    public $menuId;

    /** @var string[] */
    public $menuClass = [];

    public function __construct($data) {
        foreach ($data["items"] as $item) {
            $this -> items[] = new MMenuItem($item);
        }
        $this -> containerClass = $data["containerClass"];
        $this -> menuId = $data["menuId"];
        $this -> menuClass = $data["menuClass"];
    }
}