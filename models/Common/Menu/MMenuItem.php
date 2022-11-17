<?php
namespace Rehike\Model\Common\Menu;

class MMenuItem {
    /** @var string */
    public $label;

    /** @var string[] */
    public $class = [];

    /** @var bool */
    public $hasIcon = false;

    public function __construct($data) {
        foreach ($data as $key => $val) {
            $this -> {$key} = $val;
        }
    }
}