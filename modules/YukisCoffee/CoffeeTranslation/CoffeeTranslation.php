<?php
namespace YukisCoffee\CoffeeTranslation;

use YukisCoffee\CoffeeTranslation\{
    Attributes\StaticClass,
    Configuration\Configuration,
    Router\IRouter,
    Router\SimpleRouter,
    Lang\LanguageApi,
    Lang\NamespaceBoundLanguageApi
};

/**
 * A simple PHP internationalization framework.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
final class CoffeeTranslation extends LanguageApi
{
    /**
     * Denotes the current version of the framework.
     */
    private const VERSION = "0.1";

    /**
     * Stores whether or not the runtime environment supports the multibyte
     * string extension (mbstring).
     */
    private static bool $isMbStringSupported = false;

    /**
     * Stores the user configuration for the current session.
     */
    private static Configuration $config;

    /**
     * Stores the current router handler to use.
     * 
     * The user may replace this.
     * 
     * @see setRouter()
     */
    private static IRouter $router;

    // Disable instantiation
    private function __construct() {}

    /**
     * Initialize the framework.
     */
    public static function _init(): void
    {
        self::$config = new Configuration;
        self::$router = new SimpleRouter;

        self::$isMbStringSupported = extension_loaded("mbstring");
    }

    /**
     * Get the currently used version of the framework.
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Gets the global configuration interface for the framework.
     */
    public static function getConfigApi(): Configuration
    {
        return self::$config;
    }

    /**
     * Get the global router handler.
     */
    public static function getRouter(): IRouter
    {
        return self::$router;
    }

    /**
     * Set the global router handler to be used.
     */
    public static function setRouter(IRouter $newRouter): void
    {
        self::$router = $newRouter;
    }

    /**
     * Check if the current runtime supports the mbstring extension.
     * 
     * @internal
     */
    public static function getIsMbStringSupported(): bool
    {
        return self::$isMbStringSupported;
    }

    public static function getNamespace(string $namespace): NamespaceBoundLanguageApi
    {
        return new NamespaceBoundLanguageApi($namespace);
    }
}

CoffeeTranslation::_init();