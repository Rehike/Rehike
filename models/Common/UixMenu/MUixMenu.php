<?php
namespace Rehike\Model\Common\UixMenu;

class MUixMenu {
    /** @var string[] */
    public $class;

    /** @var MButton */
    public $triggerBtn;

    /** @var MUiMenuContent */
    public $menu;

    /** @var boolean */
    public $flipped;

    /** @var boolean */
    public $hideUntilDelayloaded;

    public function __construct($data) {
        $this -> class = $data -> class ?? [];
        $this -> triggerBtn = $data -> triggerBtn ?? null;
        $this -> menu = $data -> menu ?? null;
        $this -> flipped = $data -> flipped ?? false;
        $this -> hideUntilDelayloaded = $data -> hideUntilDelayloaded ?? false;
    }
}