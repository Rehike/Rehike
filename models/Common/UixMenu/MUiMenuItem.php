<?php
namespace Rehike\Model\Common\UixMenu;

class MUiMenuItem {
    /** @var string */
    public $text;

    /** @var string[] */
    public $attr;

    /** @var string[] */
    public $class;

    /** @var string */
    public $endpoint;

    /** @var string */
    public $params;

    /** @var boolean */
    public $closeOnClick;

    /** @var boolean */
    public $hasIcon;

    public function __construct($data) {
        $this -> text = $data -> text ?? "";
        $this -> attr = $data -> attr ?? [];
        $this -> class = $data -> class ?? [];
        $this -> endpoint = $data -> endpoint ?? "";
        $this -> params = $data -> params ?? "";
        $this -> closeOnClick = $data -> closeOnClick ?? false;
        $this -> hasIcon = $data -> hasIcon ?? false;
    }

    /**
     * Build a menu item from InnerTube data.
     * Handy for converting the endpoint to an AJAX action URL,
     * as well as easy grabbing of params.
     */
    public function fromData($item) {

    }
}