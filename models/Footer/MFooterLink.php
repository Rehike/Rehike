<?php
namespace Rehike\Model\Footer;
use \Rehike\Model\Footer\Converter;

class MFooterLink {
    /** @var string */
    public $text = "";

    /** @var string */
    public $href = "";

    public function __construct($data) {
        $this -> text = $data -> text ?? "";
        $this -> href = $data -> href ?? "";
    }
}