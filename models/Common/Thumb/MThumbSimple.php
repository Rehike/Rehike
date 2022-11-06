<?php
namespace Rehike\Model\Common\Thumb;

class MThumbSimple {
    public $type = "simple";

    /** @var string */
    public $image;

    /** @var double */
    public $width;

    /** @var double */
    public $height;

    /** @var string */
    public $alt;

    /** @var bool */
    public $delayload = false;

    public function __construct($data) {
        $this -> image = $data["image"] ?? "";
        $this -> width = $data["width"] ?? 0;
        $this -> height = $data["height"] ?? 0;
        $this -> alt = $data["alt"] ?? "";
        $this -> delayload = $data["delayload"] ?? false;
    }
}