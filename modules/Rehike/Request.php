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
    use \Rehike\RequestTypes\DataApiRequest;

    // Constant namespace identifiers
    // These are used for on response callbacks
    // for different request types.
    const NS_INNERTUBE = "NS_INNERTUBE";
    const NS_URL = "NS_URL";
    const NS_INITIALDATA = "NS_INITIALDATA";
    const NS_DATAAPI = "NS_DATAAPI";

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

    public static $innertubeHeaders = [];
    
    public static function __initStatic()
    {
        Request::init();
    }

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
            self::$requestManagers[$namespace]->requestsMaxAttempts = 1;
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
     * Enable authentication from the signin service.
     * 
     * @return void
     */
    public static function useAuth()
    {
        if (AuthManager::shouldAuth())
        {
            self::$innertubeHeaders += [
                "Authorization" => AuthManager::getAuthHeader(),
                "Origin" => "https://www.youtube.com",
                "Host" => "www.youtube.com",
                "User-Agent" => $_SERVER['HTTP_USER_AGENT']  ?? ""
            ];
        }
    }

    /**
     * Use the account's gaia ID for authenticating
     * InnerTube requests.
     * 
     * @return void
     */
    public static function authUseGaiaId()
    {
        $gaiaId = AuthManager::getGaiaId();
            
        /*
         * No GAIA ID is reported for channels associated with the Google
         * account itself. Only brand accounts must account for the distinction.
         */
        if ("" != $gaiaId)
        {
            self::$innertubeHeaders += [
                /*
                 * TODO(dcooper): Invalid AuthUser use.
                 * 
                 * AuthUser is used to switch between Google accounts (i.e.
                 * Gmail addresses themselves) and should not be hardcoded as
                 * zero as this will result in the wrong account being used by
                 * Rehike.
                 */
                "X-Goog-AuthUser" => "0",
                "X-Goog-PageId" => $gaiaId
            ];
        }
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
     * Wrap a set of functions to operate in a separate namespace
     * as the rest of the code.
     * 
     * Essentially, this implements a convenient namespace switch
     * because our code isn't bad enough as it is (actually this
     * makes it more bearable).
     * 
     * @param string $namespace to switch to
     * @param callback $cb (what to do)
     * @return mixed
     */
    public static function namespaceWrap($namespace, $cb)
    {
        // Switch namespace for a single request
        $previousNS = self::getNamespace();
        self::setNamespace($namespace);

        $response = $cb();

        self::setNamespace($previousNS);

        return $response;
    }

    /**
     * A useful wrapper for generating single request functions.
     * 
     * @param callback $cb (adds the request to queue)
     * @return mixed
     */
    public static function singleRequestWrapper($cb)
    {
        return self::namespaceWrap("_singleRequest", function() use ($cb) {
            $cb();
            
            return self::getResponses()["singleRequest"];
        });
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
        static $final = [];

        // Find namespace for handling
        foreach (self::$namespacedRequestMap[self::getNamespace()] as $id => $namespace)
        {
            $me = @$responses["{$namespace}_{$id}"];
            if (!isset($me)) continue; // assume errored

            // Prevent impossible to find bugs by the static piece of shit not fucking
            // wiping itself because some stupid fucking dumbass decided to actually
            // make this fucking request manager instead of using something better
            // someone else already made like a normal fucking human being. Fuck you
            // Taniko and I wish only the greatest pain on you for writing this pile
            // of shit that's been a thorn in our collective backs for years. FUCK YOU
            // FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU
            // FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FUCK YOU FCUK YOU FKC OUY K UGF OUY KO UYOUCKOU
            if (isset($final[$id])) unset($final[$id]);

            switch ($namespace)
            {
                case self::NS_INITIALDATA:
                    $final += [$id => self::handleInitialDataResponse($me)];
                    break;
                case self::NS_INNERTUBE:
                case self::NS_URL:
                case self::NS_DATAAPI:
                default:
                    $final += [$id => $me];
                    break;
            }
        }

        self::$namespacedRequestMap[self::$requestNamespace] = [];

        return $final;
    }
}