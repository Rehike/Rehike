<?php
namespace Rehike\Model\Common;

/**
 * Implements the common button model
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MButton
{
    public $style = "default";
    public $size = "default";
    public $hasIcon = false;
    public $noIconMarkup = false;
    public $tooltip;
    public $class = [];
    public $attributes = [];
    public $accessibilityAttributes = [];
    public $disabled = false;
    public $content;

    public function __construct($array = [])
    {
        $this->content = (object)["runs" => []];

        foreach ($array as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    protected function setText($string)
    {
        $this->content = (object)[
            "runs" => [(object)[
                "text" => $string
            ]]
        ];
    }

    protected function addRun($object)
    {
        $this->content->runs[] = $object;
    }
}