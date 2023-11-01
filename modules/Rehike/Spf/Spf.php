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
            string $templateName,
            string $blockBound
    ): Element
    {
        self::$elementRegistry[$id] = new Element($templateName, $blockBound);
        return self::$elementRegistry[$id];
    }

    /**
     * Alias for getElementById.
     */
    public static function element(string $id): ?Element
    {
        return self::getElementById($id);
    }
}