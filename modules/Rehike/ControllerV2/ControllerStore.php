<?php
namespace Rehike\ControllerV2;

/**
 * Implements session caching functionality for imported
 * Controller v2 controller modules.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ControllerStore
{
    /** @var object[] */
    protected static $items = [];

    /** @return bool */
    public static function hasController($name)
    {
        return isset(self::$items[$name]);
    }

    /** @return object|null */
    public static function getController($name)
    {
        return self::$items[$name] ?? null;
    }

    /**
     * Add a controller to the session cache.
     * 
     * @return void
     */
    public static function registerController($name, $controllerImport)
    {
        self::$items += [$name => $controllerImport];
    }
}