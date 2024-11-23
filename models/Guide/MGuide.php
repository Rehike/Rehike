<?php
namespace Rehike\Model\Guide;

// Async imports:
use function Rehike\Async\async;
use Rehike\Async\Promise;

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
     * @return Promise<void>
     */
    public static function fromData($data): Promise/*<void>*/
    {
        return async(function() use ($data) {
            $me = new self();

            $me->items = yield Converter::fromData($data);

            return $me;
        });
    }
}