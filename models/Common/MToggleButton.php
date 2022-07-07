<?php
namespace Rehike\Model\Common;

/**
 * Implements a model for the common toggle button
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MToggleButton extends MButton
{
    protected $hideNotToggled = false;

    public $isToggled = false;

    public function __construct($isToggled = false, $array = [])
    {
        parent::__construct();

        $this->isToggled = $isToggled;

        if ($this->hideNotToggled && !$isToggled)
        {
            $this->class[] = "hid";
        }
    }
}