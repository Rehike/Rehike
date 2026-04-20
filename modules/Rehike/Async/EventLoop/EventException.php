<?php
declare(strict_types=1);
namespace Rehike\Async\EventLoop;

use Exception, Throwable;

/**
 * Associates an exception with an event.
 * 
 * @author Leymonaide <pumpkinpielemon@gmail.com>
 * @author The Rehike Maintainers
 */
class EventException extends Exception
{
    public function __construct(Event $event, Throwable $previous)
    {
        $eventTextualIdentifier = "an unknown event";
        if (($trackingCookie = $event->getTrackingCookie()))
        {
            $eventTextualIdentifier = "the event with tracking cookie \"$trackingCookie\"";
        }

        parent::__construct("An exception occurred in an event $eventTextualIdentifier.", 0, $previous);
    }
}