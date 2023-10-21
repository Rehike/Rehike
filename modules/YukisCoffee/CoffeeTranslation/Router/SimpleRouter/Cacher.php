<?php
namespace YukisCoffee\CoffeeTranslation\Router\SimpleRouter;

use YukisCoffee\CoffeeTranslation\Attributes\StaticClass;
use YukisCoffee\CoffeeTranslation\Lang\Record\LanguageRecord;

/**
 * Manages caching for SimpleRouter.
 * 
 * The only caching done at all is within a session. With SimpleRouter, parsing
 * is only performed once for a single cache file, and then the result's cached
 * here for subsequent access.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
final class Cacher
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Maps a given effective URI position to a LanguageRecord.
     * 
     * @var LanguageRecord[]
     */
    private static array $cacheMap = [];

    /**
     * Inserts a new item into the cache map.
     */
    public static function insert(string $name, LanguageRecord $value): void
    {
        self::$cacheMap += [$name => $value];
    }

    /**
     * Checks if the cache map has the requested key.
     */
    public static function has(string $name): bool
    {
        return isset(self::$cacheMap[$name]);
    }

    /**
     * Retrieves a value from the cache.
     */
    public static function get(string $name): ?LanguageRecord
    {
        return self::$cacheMap[$name] ?? null;
    }
}