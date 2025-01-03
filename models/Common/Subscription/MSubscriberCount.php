<?php
namespace Rehike\Model\Common\Subscription;

/**
 * Implements a model for the subscription count element
 * that shows next to the actions.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MSubscriberCount
{
    public $simpleText;
    public $branded = true;
    public $direction = "horizontal";

    public function __construct($text, $branded = true, $direction = "horizontal")
    {
        $this->simpleText = $text;
        $this->branded = $branded;
        $this->direction = $direction;
    }
}