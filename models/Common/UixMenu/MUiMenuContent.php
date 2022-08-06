<?php
namespace Rehike\Model\Common\UixMenu;

class MUiMenuContent {
    /** @var MUiMenuItem[] */
    public $items;

    public function __construct($items) {
        $this -> items = $items ?? [];
    }
}