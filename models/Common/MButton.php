<?php
namespace Rehike\Model\Common;

/**
 * Implements the common button model
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
#[\AllowDynamicProperties]
class MButton
{
    public $style = "STYLE_DEFAULT";
    public $size = "SIZE_DEFAULT";
    public $icon;
    public $tooltip;
    public $class = [];
    public $attributes = [];
    public $accessibility;
    public $isDisabled = false;

    public function __construct($array = [])
    {
        $this->text = (object)["runs" => []];

        foreach ($array as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    protected function setText($string)
    {
        $this->text = (object)[
            "runs" => [(object)[
                "text" => $string
            ]]
        ];
    }

    protected function addRun($object)
    {
        $this->text->runs[] = $object;
    }
}