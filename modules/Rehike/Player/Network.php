<?php
namespace Rehike\Player;

require_once "Constants.php";

// --- REHIKE-SPECIFIC IMPORTS ---
use YukisCoffee\CoffeeRequest\CoffeeRequest;
use YukisCoffee\CoffeeRequest\Enum\PromiseStatus;
// -------------------------------

/**
 * A wrapper class for networking. 
 * 
 * This allows portability of this portion of Rehike for freer 
 * reuse in other projects, without them having to include YukisCoffee
 * modules.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Network
{
    const COFFEEREQUEST_LIBRARY = "YukisCoffee\\CoffeeRequest\\CoffeeRequest";

    private static string $mode = "curl";
    private static CoffeeRequest $coffeeRequest;

    /**
     * Initialise the class
     */
    public static function init(): void
    {
        self::determineMode();
    }

    /**
     * Perform a network request.
     */
    public static function request(string $url): string
    {
        switch (self::$mode)
        {
            case "curl": return self::curlRequest($url);
            case "coffee": return self::coffeeRequest($url);
        }
    }

    /**
     * Perform a network request using the CoffeeRequest library.
     * 
     * This is used within Rehike itself or when CoffeeRequest is 
     * otherwise available.
     */
    protected static function coffeeRequest(string $url): string
    {
        $p = CoffeeRequest::request($url);

        do
        {
            CoffeeRequest::run();
        }
        while (PromiseStatus::PENDING == $p->status);

        return $p->result;
    }

    /**
     * Perform a network request using CURL.
     * 
     * This is used as a fallback when CoffeeRequest is not
     * available, such as when used by a third party project.
     */
    protected static function curlRequest(string $url): string
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => false,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Set the internal request method.
     * 
     * The available options are
     *    - coffee (for CoffeeRequest)
     *    - curl (for cURL)
     */
    protected static function determineMode(): void
    {
        if (self::coffeeAvailable())
        {
            self::$mode = "coffee";
        }
        else
        {
            self::$mode = "curl";
        }
    }

    /**
     * Check the availability of CoffeeRequest.
     */
    protected static function coffeeAvailable(): bool
    {
        return class_exists(self::COFFEEREQUEST_LIBRARY);
    }
}

Network::init();