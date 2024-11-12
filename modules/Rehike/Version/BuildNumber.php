<?php
namespace Rehike\Version;

/**
 * Utilities for calculating the build number of Rehike.
 * 
 * This is used since May 2024 in favour of using the commit number directly,
 * since that system wasn't entirely accurate anyway.
 * 
 * This system is like that used by pre-Steampipe Source games and other
 * Network Neighborhood projects like KappaBar.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class BuildNumber
{
    /**
     * The time the build number system was born.
     * 
     * 2024/05/21
     */
    const BUILDNUM_EPOCH = 1716249600;
    
    /**
     * The cutoff build number for the legacy system.
     * 
     * This number is the starting point for the new build number system,
     * so that the sequence is kept.
     */
    const LEGACY_BUILDNUM_CUTOFF_NUM = 1525;
    
    /**
     * Gets the build number.
     */
    public static function getBuildNumber(): int
    {
        $lastUpdateTime = VersionController::$versionInfo->time;
        $diff = $lastUpdateTime - self::BUILDNUM_EPOCH;
        
        $baseNum = floor($diff / (60 * 60 * 24));
        
        return self::LEGACY_BUILDNUM_CUTOFF_NUM + $baseNum;
    }
}