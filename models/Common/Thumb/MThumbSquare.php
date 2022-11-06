<?php
namespace Rehike\Model\Common\Thumb;

class MThumbSquare extends MThumbSimple {
    public $type = "square";

    public function __construct($data) {
        $this -> image = $data["image"] ?? "";
        $this -> width = $data["size"] ?? 0;
        $this -> height = $data["size"] ?? 0;
        $this -> alt = $data["alt"] ?? "";
        $this -> delayload = $data["delayload"] ?? false;
    }
}