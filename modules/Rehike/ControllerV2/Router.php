<?php
namespace Rehike\ControllerV2;

use Rehike\Boot\Bootloader;
use Rehike\ControllerV2\Util\GlobToRegexp;
use Rehike\SimpleFunnel;

/**
 * Implements the Controller V2 router.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
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
     * @param string[] $defs
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
            return self::invokeHandlerForMatch($definitions[$bestMatch], $method);
        }

        // If the current URI does not exist, fall back to the
        // default condition.
        if (isset($definitions["default"]))
        {
            return self::invokeHandlerForMatch($definitions["default"], $method);
        }
    }

    /**
     * Invokes the controller handler for a match to a class.
     * 
     * @param string|callable $pointer
     * @param string $method that now finally gets used by the module!
     * @return mixed|void
     */
    protected static function invokeHandlerForMatch(
            string|callable $pointer, 
            string $method
    )
    {
        if (is_callable($pointer))
        {
            // Premature return since this is a unique case
            // XXX (kawapure): This style of handling keeps the arguments from CV2 for now.
            // This should be reworked in the future to abandon the CV2 style. No function
            // controller in Rehike makes use of these arguments or even acknowledges them,
            // as the feature is only used to hastily exit.
            return $pointer(Core::$state, Core::$template, new RequestMetadata());
        }
        else if (is_string($pointer))
        {
            // String pointers are class name references, so we will query for information
            // about this class.
            if (!class_exists($pointer))
            {
                throw new \Exception("Controller class does not exist: " . $pointer);
            }
            
            if (!in_array(IController::class, class_implements($pointer)))
            {
                throw new \Exception("Attempting to use non-controller class \"$pointer\" as controller.");
            }
            
            $isControllerAsync =
                in_array(IGetControllerAsync::class, class_implements($pointer)) ||
                in_array(IPostControllerAsync::class, class_implements($pointer));
            
            if ($method == "get")
            {
                $className = $isControllerAsync
                    ? IGetControllerAsync::class
                    : IGetController::class;
                
                if (!in_array($className, class_implements($pointer)))
                {
                    throw new \Exception("Using non-GET controller \"$pointer\" as GET controller.");
                }
            }
            else if ($method == "post")
            {
                $className = $isControllerAsync
                    ? IPostControllerAsync::class
                    : IPostController::class;
                
                if (!in_array($className, class_implements($pointer)))
                {
                    throw new \Exception("Using non-POST controller \"$pointer\" as POST controller.");
                }
            }
            
            /**
             * @var IController
             */
            $instance = new $pointer();
            $instance->initializeController(new RequestMetadata());
            
            if ($method == "get")
            {
                if ($isControllerAsync)
                {
                    /** @var IGetControllerAsync */
                    $getControllerInstance = $instance;
                    Bootloader::handleAsyncControllerRequest($getControllerInstance->getAsync());
                    return;
                }
                else
                {   
                    /** @var IGetController */
                    $getControllerInstance = $instance;
                    return $getControllerInstance->get();
                }
            }
            else if ($method == "post")
            {
                if ($isControllerAsync)
                {
                    /** @var IPostControllerAsync */
                    $postControllerInstance = $instance;
                    Bootloader::handleAsyncControllerRequest($postControllerInstance->postAsync());
                    return;
                }
                else
                {
                    /** @var IPostController */
                    $postControllerInstance = $instance;
                    return $postControllerInstance->post();
                }
            }
        }
        else
        {
            $type = gettype($pointer);

            throw new Exception\RouterInvalidPointerException(
                "Controller pointer of type $type is not supported."
            );
        }
        
        throw new \Exception("Should never get here.");
    }
}