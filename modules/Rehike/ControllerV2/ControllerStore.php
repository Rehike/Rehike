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
    protected static array $items = [];

    public static function hasController(string $name): bool
    {
        return isset(self::$items[$name]);
    }

    public static function getController(string $name): ?object
    {
        return self::$items[$name] ?? null;
    }

    /**
     * Add a controller to the session cache.
     */
    public static function registerController(
            string $name, 
            object $controllerImport
    ): void
    {
        self::$items += [$name => $controllerImport];
    }
}