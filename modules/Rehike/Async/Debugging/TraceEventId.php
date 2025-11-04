<?php
namespace Rehike\Async\Debugging;

// Component IDs:
const CID_EVENT = 0;
const CID_EVENTLOOP = 1;
const CID_PROMISE = 2;
const CID_QUEUEDPROMISE = 3;
const CID_ASYNCFUNC = 4;

// Status codes.
const TE_SUCCESS = 0;
const TE_FAIL = 1;

// Internal structure offsets.
const TEID_SEV = 31;
const TEID_COM = 15;

/**
 * These have an internal structure of:
 *  
 *  ---------------------------------------------
 *  | Bit range |    31    |   30-16   |  15-0  |
 *  |-----------|----------|-----------|--------|
 *  |   Field   | Severity | Component | Status |
 *  ---------------------------------------------
 * 
 * A severity code of 0 indicates a success; 1 indicates a failure.
 * 
 * The component is equal to one of the CID_ constants.
 * 
 * The status is a unique value to the component.
 * 
 * @enum
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class TraceEventId
{
    public const EventCreate   = (TE_SUCCESS << TEID_SEV) | (CID_EVENT << TEID_COM) | 0;
    public const EventDestroy  = (TE_SUCCESS << TEID_SEV) | (CID_EVENT << TEID_COM) | 1;
    public const EventRun      = (TE_SUCCESS << TEID_SEV) | (CID_EVENT << TEID_COM) | 2;
    public const EventFulfill  = (TE_SUCCESS << TEID_SEV) | (CID_EVENT << TEID_COM) | 3;
    
    public const EventLoopRun                  = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 0;
    public const EventLoopPause                = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 1;
    public const EventLoopUnpause              = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 2;
    public const EventLoopDropOut              = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 3;
    public const EventLoopAdd                  = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 4;
    public const EventLoopRemove               = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 5;
    public const EventLoopCull                 = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 6;
    public const EventLoopQueuedPromiseAdd     = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 7;
    public const EventLoopQueuedPromiseClear   = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 8;
    public const EventLoopCycleComplete        = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 9;
    public const EventLoopEventNullified       = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 10;
    public const EventLoopFinishQueuedPromises = (TE_SUCCESS << TEID_SEV) | (CID_EVENTLOOP << TEID_COM) | 11;
    
    public const PromiseCreate       = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 0;
    public const PromiseDestroy      = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 1;
    public const PromiseThen         = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 2;
    public const PromiseCatch        = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 3;
    public const PromiseResolve      = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 4;
    public const PromiseReject       = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 5;
    public const PromiseDeferResolve = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 6;
    public const PromiseDeferReject  = (TE_SUCCESS << TEID_SEV) | (CID_PROMISE << TEID_COM) | 7;
    
    public const QueuedPromiseCreate  = (TE_SUCCESS << TEID_SEV) | (CID_QUEUEDPROMISE << TEID_COM) | 0;
    public const QueuedPromiseDestroy = (TE_SUCCESS << TEID_SEV) | (CID_QUEUEDPROMISE << TEID_COM) | 1;
    public const QueuedPromiseFinish  = (TE_SUCCESS << TEID_SEV) | (CID_QUEUEDPROMISE << TEID_COM) | 2;
    
    public const AsyncFunctionCreate      = (TE_SUCCESS << TEID_SEV) | (CID_ASYNCFUNC << TEID_COM) | 0;
    public const AsyncFunctionDestroy     = (TE_SUCCESS << TEID_SEV) | (CID_ASYNCFUNC << TEID_COM) | 1;
    public const AsyncFunctionRun         = (TE_SUCCESS << TEID_SEV) | (CID_ASYNCFUNC << TEID_COM) | 2;
    public const AsyncFunctionCatchUnderlyingException = (TE_SUCCESS << TEID_SEV) | (CID_ASYNCFUNC << TEID_COM) | 3;
    public const AsyncFunctionFinishingUp = (TE_SUCCESS << TEID_SEV) | (CID_ASYNCFUNC << TEID_COM) | 4;
    
    public static function getStatusAlone(int $eid): int
    {
        return $eid & TEID_COM + 1;
    }
    
    public static function getComponent(int $eid): int
    {
        return ($eid >> TEID_COM) & 0xFFFFFF;
    }
    
    public static function success(int $eid): bool
    {
        return ($eid >> 31) == 0;
    }
    
    public static function failed(int $eid): bool
    {
        return !self::success($eid);
    }
    
    public static function makeSuccessful(int $eid): int
    {
        return $eid & ~(TE_SUCCESS << TEID_SEV);
    }
    
    public static function makeFailed(int $eid): int
    {
        return $eid | (TE_FAIL << TEID_SEV);
    }
}