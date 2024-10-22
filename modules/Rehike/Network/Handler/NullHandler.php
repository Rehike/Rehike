<?php
namespace Rehike\Network\Handler;

use Rehike\Attributes\Override;

use Rehike\Network\IRequest;
use Rehike\Network\Exception\NoSupportedHandlerException;

use Generator;

/**
 * Implements a "null" handler to be used in the event no
 * available handler is possible.
 * 
 * This may only throw an error notifying the user of a lack of
 * any supported interface.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class NullHandler extends NetworkHandler
{
    #[Override]
    public function addRequest(IRequest $r): void
    {
        throw new NoSupportedHandlerException();
    }

    #[Override]
    public function clearRequests(): void {}

    /** @return Generator<void> */
    public function onRun(): Generator/*<void>*/
    {
        if (false) yield;
        $this->fulfill();
    }
}