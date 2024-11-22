<?php
namespace Rehike\ControllerV2;

use Rehike\ControllerV2\Util\GlobToRegexp;
use Rehike\SimpleFunnel;

/**
 * Implements the Controller V2 router.
 * 
 * @author The Rehike Maintainers
 */
class Router
{
    private static array $debugLog = [
        "type" => "none",
        "route" => ""
    ];

    /**
     * Get internal debug information for logging purposes.
     * 
     * @internal
     */
    public static function getInternalDebug(): array
    {
        return self::$debugLog;
    }

    /**
     * Configure router definitions for all GET requests.
     * 
     * @param string[] $defs
     * @return mixed|void
     */
    public static function get(array $defs)
    {
        if ("GET" == $_SERVER['REQUEST_METHOD'])
        {
            return self::baseRequestMethod($defs, "get");
        }
    }

    /**
     * Configure router definitions for all POST requests.
     * 
     * @param string[] $defs
     * @return mixed|void
     */
    public static function post(array $defs)
    {
        if ("POST" == $_SERVER['REQUEST_METHOD'])
        {
            return self::baseRequestMethod($defs, "post");
        }
    }

    /**
     * Configure router definitions for redirections.
     * 
     * @param string[]|callback[] $defs
     */
    public static function redirect(array $defs): void
    {
        // Iteration the definitions tree and check the contents.
        foreach ($defs as $def => $redir)
        {
            // Convert the current definition to a regex for
            // comparison
            $regexp = GlobToRegexp::convert($def, $_SERVER["REQUEST_URI"], 0);
            
            if (preg_match($regexp, $_SERVER["REQUEST_URI"]))
            {
                // If there's a match, check if it should callback a function
                // or use the simple behaviour and redirect to a direct string
                // path.
                /** @var string $endpoint */
                $endpoint;
                if (is_callable($redir))
                {
                    $endpoint =  $redir(new RequestMetadata());

                    // Prevent undefined behaviour
                    // Also allow for some programmer freedom I guess
                    if (!is_string($endpoint)) return;
                }
                else
                {
                    // Otherwise perform the redirection using internal behaviour.
                    $endpoint = preg_replace($regexp, $redir, $_SERVER["REQUEST_URI"]);
                }

                self::$debugLog = [
                    "type" => "redirect",
                    "route" => $endpoint
                ];

                // Callback the custom handler, or fallback to the
                // default implementation.
                if (is_callable(CallbackStore::$handleRedirect))
                {
                    (CallbackStore::$handleRedirect)($endpoint);
                }
                else
                {
                    header("Location: $endpoint");
                    die();
                }
            }
        }
    }

    /**
     * Configure router definitions for request funnelling.
     * 
     * These essentially bypass Rehike.
     * 
     * They request the same request URI on www.youtube.com and return
     * the same response as given. This is useful for some things, such
     * as static resources and API access.
     * 
     * @param string[] $defs
     */
    public static function funnel(array $defs): void
    {
        // PATCH (izzy): In the case where a request sends too many PHP variables and causes
        // a warning to be printed to the output, we want to discard that. Hence, we start
        // output buffering for funnelled requests:
        while (ob_get_level() > 0)
        {
            ob_end_clean();
        }
        ob_start();
        
        foreach ($defs as $index => $value)
        {
            $pattern = $value;

            if ("~" == $pattern[0])
            {
                if (GlobToRegexp::doMatch(
                    substr($pattern, 1),
                    explode("?", $_SERVER["REQUEST_URI"])[0])
                )
                {
                    // The following URL should not be matched at all, so return.
                    return;
                }
            }

            if (GlobToRegexp::doMatch($pattern, explode("?", $_SERVER["REQUEST_URI"])[0]))
            {
                self::$debugLog = [
                    "type" => "funnel",
                    "route" => $pattern
                ];

                SimpleFunnel::funnelCurrentPage()->then(fn($r) => $r->output());
            }
        }
    }

    /**
     * Iterate the router definitions and call the best match.
     * 
     * The best match is determined through iterating through
     * the whole array before calling the method.
     * 
     * @param string[] $definitions
     * @param string $method
     * @return mixed|void
     */
    protected static function baseRequestMethod(
            array $definitions, 
            string $method
    )
    {
        $bestMatch = null;

        // Iterate the array and look for a match.
        foreach ($definitions as $def => $_val)
        if (GlobToRegexp::doMatch($def, explode("?", $_SERVER["REQUEST_URI"])[0]))
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
     * @return mixed|void
     */
    protected static function pointerHandler(
            string|callable $pointer, 
            string $method
    )
    {
        /** @var GetControllerInstance $import */
        $import;
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
                self::$debugLog = [
                    "type" => "GET",
                    "route" => $path
                ];

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