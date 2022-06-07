<?php
namespace SpfPhp;

/**
 * Implements the behaviours for the direct render
 * functionality.
 * 
 * This layer is communicated with by the main parser
 * code, so non-managerial functionality should all
 * be implemented there.
 * 
 * @author The Rehike Maintainers
 * @license MIT
 */
class DirectRender
{
    /**
     * An associative array storing a map of registered
     * direct render callbacks.
     * 
     * @var array
     */
    private static $directRenderCallbacks = [];

    /**
     * Add a direct render callback to the registry
     * 
     * @param string $name
     * @param Callable $cb
     * @return void
     */
    public static function registerCallback($name, $cb)
    {
        self::$directRenderCallbacks += [$name => $cb];
    }

    /**
     * Get a callback by name and return a syntax wrapper.
     * 
     * @param string $name
     * @return DirectRenderSyntaxWrapper
     */
    public static function getCallback($name)
    {
        $callback = null;

        if (isset(self::$directRenderCallbacks[$name]))
        {
            $callback = self::$directRenderCallbacks[$name];
        }

        // Wrap the callback in a DirectRenderSyntaxWrapper
        // This simply exists to clean up syntax where resulting
        // in a double function call, like
        // getCallback("my_callback")($xtags)
        // becomes
        // getCallback("my_callback")->call($xtags)
        return new DirectRenderSyntaxWrapper($callback);
    }
}