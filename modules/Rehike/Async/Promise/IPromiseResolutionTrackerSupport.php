<?php
namespace Rehike\Async\Promise;

/**
 * Support interface for PromiseResolutionTracker.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IPromiseResolutionTrackerSupport
{
    public function throwsOnUnresolved(): bool;
}