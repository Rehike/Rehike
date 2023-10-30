<?php
namespace Rehike;

/**
 * Stores resource constants locations for resource routing in Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ResourceConstantsStore
{
    public static object $constants;

    public static function init(): void
    {
        self::$constants = include "includes/resource_constants.php";
    }

    public static function get(): object
    {
        return self::$constants;
    }
}