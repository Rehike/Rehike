<?php
namespace Rehike;

/**
 * Stores resource constants locations for resource routing in Rehike.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class ResourceConstantsStore
{
    public static object $constants;
    private static object $rehikeConstants;

    public static function init(): void
    {
        self::$constants = include "includes/resource_constants.php";
        self::$rehikeConstants = json_decode(
            FileSystem::getFileContents("includes/static_version_map.json")
        );
    }

    /**
     * Get the resource map for native YouTube resources.
     */
    public static function get(): object
    {
        return self::$constants;
    }
    
    /**
     * Get the static resource version map for custom Rehike resources.
     */
    public static function getVersionMap(): object
    {
        return self::$rehikeConstants;
    }
}