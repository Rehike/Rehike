<?php
namespace Rehike\Async\EventLoop;

/**
 * Flags that affect runtime behaviour of events in the event loop.
 * 
 * @enum
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class EventFlags
{
    public const None = 0;
    
    /**
     * This event is suspended. Events are not executed at all while they are
     * in a suspended state. However, unlike fulfilled events, their presence
     * is maintained indefinitely.
     */
    public const Suspended = 1 << 1;
    
    /**
     * This event should not block the event loop if no events remain without
     * this flag, in order to allow queued promises to be resolved.
     * 
     * This flag is intended for use by events that wrap (and thus depend on)
     * promises in order to avoid deadlocking.
     */
    public const AllowQueuedPromisesToPass = 1 << 2;
    
    /**
     * This event may be unfulfilled at a later point in time. This event ensures
     * that the event runtime will not clean up this event, even if it is already
     * fulfilled.
     * 
     * If this flag is set, then the user is responsible for managing the lifetime
     * of the event in the event loop.
     */
    public const MayResetFulfillment = 1 << 3;
    
    /**
     * This event is not affected by the standard run limit (of a few ten-thousand times).
     * 
     * Events specifying this flag should be careful to always fulfill or suspend
     * themselves when they're not necessary in order to avoid blocking the event
     * loop from exiting.
     */
    public const NoRunLimit = 1 << 4;
}