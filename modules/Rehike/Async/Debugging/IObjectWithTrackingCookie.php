<?php
namespace Rehike\Async\Debugging;

/**
 * 
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
interface IObjectWithTrackingCookie
{
    /**
     * Gets this object's tracking cookie.
     */
    public function getTrackingCookie(): TrackingCookie;
}