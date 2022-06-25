<?php
namespace Rehike;

use YukisCoffee\CoffeeRequest\CoffeeRequest;
use Rehike\Signin\AuthManager;

/**
 * Implements the Rehike request manager.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Request
{
    use \Rehike\RequestTypes\InnertubeRequest;
    use \Rehike\RequestTypes\UrlRequest;
    use \Rehike\RequestTypes\InitialDataRequest;

    // Constant namespace identifiers
    // These are used for on response callbacks
    // for different request types.
    const NS_INNERTUBE = "NS_INNERTUBE";
    const NS_URL = "NS_URL";
    const NS_INITIALDATA = "NS_INITIALDATA";

    /** 
     * A namespace map for remembering the types of a queued request.
     * 
     * @var array[]
     */
    protected static $namespacedRequestMap = [];

    /**
     * A associative array of RequestManagers by namespace.
     * 
     * @var CoffeeRequest[]
     */
    protected static $requestManagers = [];

    /**
     * Stores the current namespace.
     * 
     * @var string
     */
    protected static $requestNamespace;

    public static function init()
    {
        self::setNamespace("default");
    }

    /**
     * Get the current namespace that requests will be wrote to.
     * 
     * @return string
     */
    public static function getNamespace()
    {
        return self::$requestNamespace;
    }

    /**
     * Set the namespace.
     * 
     * @param string $namespace
     * @return void
     */
    public static function setNamespace($namespace)
    {
        self::$requestNamespace = $namespace;

        if (!isset(self::$namespacedRequestMap[$namespace]))
        {
            self::$namespacedRequestMap[$namespace] = [];
            self::$requestManagers[$namespace] = new CoffeeRequest();
            self::$requestManagers[$namespace]->requestMaxAttempts = 1;
        }
    }

    /**
     * Clear the namespace back to the default.
     * 
     * @return void
     */
    public static function clearNamespace()
    {
        self::setNamespace("default");
    }

    /**
     * Get the namespace's RequestManager.
     * 
     * @return CoffeeRequest
     */
    protected static function getRequestManager()
    {
        return self::$requestManagers[self::getNamespace()];
    }

    /**
     * Add a request to the queue.
     * 
     * @param mixed[] $requestArray
     * @return void
     */
    public static function queueRequest($url, $options, $namespace, $id)
    {
        $namespacedId = "{$namespace}_{$id}";

        self::getRequestManager()->queueRequest($url, $options, $namespacedId);

        self::$namespacedRequestMap[self::$requestNamespace] += [$id => $namespace];
    }

    /**
     * A useful wrapper for generating single request functions.
     * 
     * @param callback $cb (adds the request to queue)
     * @return mixed
     */
    public static function singleRequestWrapper($cb)
    {
        // Switch namespace for a single request
        $previousNS = self::getNamespace();
        self::setNamespace("_singleRequest");

        $cb();

        $response = self::getResponses()["singleRequest"];

        self::setNamespace($previousNS);

        return $response;
    }
    
    /**
     * Handle initial data responses.
     * 
     * @param string $response
     * @return string
     */
    protected static function handleInitialDataResponse($response)
    {
        preg_match("/var ytInitialData = ({.*)?;</", $response, $matches);
        if ($matches[1])
        {
            return $matches[1];
        }
    }

    /**
     * Execute all queued requests and get the responses.
     * 
     * @return mixed[]
     */
    public static function getResponses()
    {
        $responses = self::getRequestManager()->runQueue();
        $final = [];

        // Find namespace for handling
        foreach (self::$namespacedRequestMap[self::getNamespace()] as $id => $namespace)
        {
            $me = @$responses["{$namespace}_{$id}"];
            if (!isset($me)) continue; // assume errored

            switch ($namespace)
            {
                case self::NS_INITIALDATA:
                    $final += [$id => self::handleInitialDataResponse($me)];
                    break;
                case self::NS_INNERTUBE:
                case self::NS_URL:
                default:
                    $final += [$id => $me];
                    break;
            }
        }

        return $final;
    }

    /**
     * Symlink for getResponses()
     * 
     * @deprecated
     */
    public static function getInnertubeResponses()
    {
        return self::getResponses();
    }
}

// Should be moved to __initStatic on new-mvc
Request::init();