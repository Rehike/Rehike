<?php
namespace YukisCoffee\CoffeeRequest;

use YukisCoffee\CoffeeRequest\Network\Request;
use YukisCoffee\CoffeeRequest\Network\Response;
use YukisCoffee\CoffeeRequest\Handler\NetworkHandler;
use YukisCoffee\CoffeeRequest\Handler\NetworkHandlerFactory;
use YukisCoffee\CoffeeRequest\Hooks\Hooks;
use YukisCoffee\CoffeeRequest\Exception\GeneralException;

/**
 * A simple asynchronous request library for PHP.
 * 
 * The CoffeeRequest API should be very familiar to JavaScript developers,
 * as the main API mirrors fetch and internal APIs mirror JavaScript's
 * Events system and Promises API.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @version 3.0.1
 */
final class CoffeeRequest
{
    /** 
     * The current version number.
     * 
     * @see getVersion()
     * @var string
     */
    private const VERSION = "3.1";

    /** 
     * Stores references to all currently running requests.
     * 
     * @var Request[] 
     */
    private static array $requests = [];

    /**
     * Keeps track of the number of running requests. The requests array
     * is only formally cleared when all active requests are finished.
     */
    private static int $activeRequests = 0;

    /**
     * Stores a reference to the currently used network handler.
     */
    private static NetworkHandler $handler;

    /**
     * A list of resolution definitions.
     */
    private static array $resolve = [];

    // Disable instances
    private function __construct() {}

    /**
     * Initialise the request manager.
     * 
     * @internal
     */
    public static function _init(): void
    {
        self::setNetworkHandler(NetworkHandlerFactory::getBest());
    }

    /**
     * Set the network handler/driver.
     */
    public static function setNetworkHandler(NetworkHandler $handler): void
    {
        if (isset(self::$handler))
        {
            try
            {
                Loop::removeEvent(self::$handler);
            }
            catch (GeneralException $e) {} // do nothing & hope for the best
        }

        self::$handler = $handler;
    }

    /**
     * Send a network request.
     * 
     * The network request API provided by CoffeeRequest is very
     * reminiscent of JavaScript's fetch API.
     * 
     * @param  mixed[] $opts
     * @return Promise<Response>
     */
    public static function request(
            string $url, 
            array $opts = []
    ): Promise/*<Response>*/
    {
        $request = new Request($url, $opts);

        if (self::$handler->isFulfilled())
        {
            self::$handler->restartManager();
        }

        self::addRequest($request);

        Loop::addEventIfNotAdded(self::$handler);

        self::$handler->addRequest($request);

        return $request->getPromise();
    }

    /**
     * Run the event loop.
     */
    public static function run(): void
    {
        Loop::run();
    }

    /**
     * Await all currently registered requests.
     * 
     * @return Promise<Response[]>
     */
    public static function awaitAll(): Promise/*<array>*/
    {
        // wrong $requests type, 
        // needs to be Promise<Response>[] not Response[]!!
        return Promise::all(self::$requests);
    }

    /**
     * Get the version of CoffeeRequest.
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    public static function getResolve(): array
    {
        return self::$resolve;
    }

    public static function setResolve(array $a): void
    {
        self::$resolve = $a;
    }

    /**
     * Report a finished request and decrement the internal counter.
     * 
     * It's too expensive to take in a finished request and remove its
     * reference for the array, so we keep track of the number of running
     * Requests and cleanup only when it can be guaranteed to have no
     * ramifications.
     * 
     * @internal
     */
    public static function reportFinishedRequest(): void
    {
        if (self::$activeRequests > 1)
        {
            self::$activeRequests--;
        }
        else
        {
            self::cleanup();
        }
    }

    private static function addRequest(Request $request): void
    {
        self::$requests[] = $request;
        self::$activeRequests++;
    }

    /**
     * Clean up when possible.
     */
    private static function cleanup(): void
    {
        self::$activeRequests = 0;
        self::$requests = [];
        self::$handler->clearRequests();
    }
}

CoffeeRequest::_init();