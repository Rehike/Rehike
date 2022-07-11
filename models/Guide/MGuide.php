<?php
namespace Rehike\Model\Guide;

/**
 * Implements the guide model.
 * 
 * This is a very barren class. Most of the functionality is implemented
 * in the Converter.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MGuide
{
    /**
     * An array of all the guide items.
     * 
     * @var object[]
     */
    public $items;

    /**
     * Process an InnerTube response and create a standard
     * guide response from it.
     * 
     * @return void
     */
    public static function fromData($data)
    {
        $me = new self();

        $me->items = Converter::fromData($data);

        return $me;
    }
}