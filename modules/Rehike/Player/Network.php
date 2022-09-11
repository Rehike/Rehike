<?php
namespace Rehike\Player;

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

    private static $mode = "curl";
    private static $coffeeRequest;

    /**
     * Initialise the class
     * 
     * @return void
     */
    public static function init()
    {
        self::determineMode();
    }

    /**
     * Perform a network request.
     * 
     * @param string $url
     * @return string
     */
    public static function request($url)
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
     * 
     * @param string $url
     * @return string
     */
    protected static function coffeeRequest($url)
    {
        return self::$coffeeRequest->request($url);
    }

    /**
     * Perform a network request using CURL.
     * 
     * This is used as a fallback when CoffeeRequest is not
     * available, such as when used by a third party project.
     * 
     * @param string $url
     * @return string
     */
    protected static function curlRequest($url)
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
     * 
     * @return void
     */
    protected static function determineMode()
    {
        if (self::coffeeAvailable())
        {
            self::$mode = "coffee";
            self::$coffeeRequest = new \YukisCoffee\CoffeeRequest\CoffeeRequest;
        }
        else
        {
            self::$mode = "curl";
        }
    }

    /**
     * Check the availability of CoffeeRequest.
     * 
     * @return bool
     */
    protected static function coffeeAvailable()
    {
        return class_exists(self::COFFEEREQUEST_LIBRARY);
    }
}

Network::init();