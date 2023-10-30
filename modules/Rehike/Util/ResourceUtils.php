<?php
namespace Rehike\Util;

use Rehike\ResourceConstantsStore;

use Rehike\Util\Exception\ResourceUtils\BadResourceException;

/**
 * Provides helper functions for getting resource locations.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ResourceUtils
{
    /**
     * Get the CSS path of a resource from its name.
     */
    public static function cssPath(string $name): string
    {
        $constants = ResourceConstantsStore::get();

        $css2x = false;

        // TODO: add css 2x check
        // old code: if (isset($pref->f4) && substr($pref->f4, 0, 1) == "4")

        if (isset($constants->css->{$name}))
        {
            return $constants->css->{$name};
        }
        else
        {
            throw new BadResourceException(
                "Unknown CSS resource name \"$name\""
            );
        }
    }

    /**
     * Get the JS path of a resource from its name.
     */
    public static function jsPath(string $name): string
    {
        $constants = ResourceConstantsStore::get();

        if (isset($constants->js->{$name}))
        {
            return $constants->js->{$name};
        }
        else
        {
            throw new BadResourceException(
                "Unknown JS resource name \"$name\""
            );
        }
    }

    /**
     * Get the path of a static image resource from its name.
     */
    public static function imgPath(string $name): string
    {
        $constants = ResourceConstantsStore::get();

        if (isset($constants->img->{$name}))
        {
            return $constants->img->{$name};
        }
        else
        {
            throw new BadResourceException(
                "Unknown image resource name \"$name\""
            );
        }
    }
}