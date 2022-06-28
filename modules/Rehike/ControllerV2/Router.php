<?php
namespace Rehike\ControllerV2;

include_once "modules/polyfill/fnmatch.php";

/**
 * Implements the Controller V2 router.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Router
{
    /**
     * Configure router definitions for all GET requests.
     * 
     * @param string[] $defs
     * @return mixed
     */
    public static function get($defs)
    {
        if ("GET" == $_SERVER['REQUEST_METHOD'])
        return self::baseRequestMethod($defs, "get");
    }

    /**
     * Configure router definitions for all POST requests.
     * 
     * @param string[] $defs
     * @return mixed
     */
    public static function post($defs)
    {
        if ("POST" == $_SERVER['REQUEST_METHOD'])
        return self::baseRequestMethod($defs, "post");
    }

    /**
     * Iterate the router definitions and call the best match.
     * 
     * The best match is determined through iterating through
     * the whole array before calling the method.
     * 
     * @param string[] $definitions
     * @param string $method
     * @return mixed
     */
    protected static function baseRequestMethod($definitions, $method)
    {
        $bestMatch = null;

        // Iterate the array and look for a match.
        foreach ($definitions as $def => $_val)
        if (\fnmatch($def, explode("?", $_SERVER["REQUEST_URI"])[0]))
        {
            $bestMatch = $def;
        }

        // If the best match exists (is callable), return it.
        // The variable is called best match, but this does
        // include only one match as well.
        if (!is_null($bestMatch) && !is_null($definitions[$bestMatch]))
        {
            return self::pointerHandler($definitions[$bestMatch], $method);
        }

        // If the current URI does not exist, fall back to the
        // default condition.
        if (isset($definitions["default"]))
        {
            return self::pointerHandler($definitions["default"], $method);
        }
    }

    /**
     * Handle a definition's pointer.
     * 
     * As of right now, only direct callbacks and strings 
     * pointing to a file path are supported.
     * 
     * @param string|callable $pointer
     * @param string $method that now finally gets used by the module!
     * @return mixed
     */
    protected static function pointerHandler($pointer, $method)
    {
        // Set temporary state variable for cv1 coexistence
        Core::$cv2HasBeenUsed = true;

        if (is_callable($pointer))
        {
            // Premature return since this is a unique case
            return $pointer(Core::$state, Core::$template, new RequestMetadata());
        }
        else if (is_string($pointer))
        {
            foreach ([
                "controllers/$pointer.php",
                "controllers/$pointer",
                "$pointer.php",
                $pointer
            ] as $path) if (file_exists($path) && is_file($path))
            {
                $import = Core::import($path, false);
                break;
            }
        }
        else
        {
            $type = gettype($pointer);

            throw new Exception\RouterInvalidPointerException(
                "Controller pointer of type $type is not supported."
            );
        }

        // Handle imports
        return $import->{$method}();
    }
}