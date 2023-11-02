<?php
namespace Rehike\Spf;

/**
 * The primary Rehike SPF API.
 * 
 * This must be instantiatible because it will be delegated to the Twig context
 * via `rehike.spf` too.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Spf
{
    /**
     * Configures all types of elements for the current controller.
     * 
     * @var Element[]
     */
    private static array $elementRegistry = [];

    /**
     * @return Element[]
     */
    public static function getAllElements(): array
    {
        return self::$elementRegistry;
    }

    /**
     * Get an element by its ID. If the element does not exist, this will return
     * null.
     */
    public static function getElementById(string $id): ?Element
    {
        if (isset(self::$elementRegistry[$id]))
        {
            return self::$elementRegistry[$id];
        }

        return null;
    }

    /**
     * Define a new SPF element.
     */
    public static function createElement(
            string $id,
            ?string $templateName = null,
            bool $blockBound = false
    ): Element
    {
        self::$elementRegistry[$id] = new Element(
            $id, $templateName, $blockBound
        );
        return self::$elementRegistry[$id];
    }

    /**
     * Alias for getElementById.
     */
    public static function element(string $id): ?Element
    {
        return self::getElementById($id);
    }

    /**
     * Determines if SPF is requested, and gets the state request type if so.
     */
    public static function isSpfRequested(): string|false
    {
        if (isset($_GET["spf"]))
        {
            switch ($_GET["spf"])
            {
                case "navigate":
                case "navigate-back":
                case "navigate-forward":
                case "load":
                    return $_GET["spf"];
            }
        }

        return false;
    }
}