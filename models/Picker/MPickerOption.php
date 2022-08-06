<?php
namespace Rehike\Model\Picker;

class MPickerOption {
    /** @var string */
    public $name;

    /** @var string */
    public $value;

    /** @var string */
    public $text;

    /** @var boolean */
    public $selected;

    /** 
     * Optional
     * 
     * @var string
     */
    public $flag;

    public function __construct($data) {
        $this -> name = $data -> name ?? null;
        $this -> value = $data -> value ?? null;
        $this -> text = $data -> text ?? null;
        $this -> selected = $data -> selected ?? null;
        $this -> flag = $data -> flag ?? null;
    }
}