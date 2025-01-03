<?php
namespace Rehike\Player;

require_once "Constants.php";

// --- REHIKE-SPECIFIC IMPORTS ---
use Rehike\Network\NetworkCore;
use Rehike\Async\Promise\PromiseStatus;
// -------------------------------

/**
 * A wrapper class for networking. 
 * 
 * This allows portability of this portion of Rehike for freer 
 * reuse in other projects, without them having to include Rehike's
 * heavy network + async stack.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class Network
{
    const RH_NETWORK_LIBRARY = "Rehike\\Network\\NetworkCore";

    private static string $mode = "curl";
    private static NetworkCore $rehikeNetworkCore;

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
            case "rehike": return self::rehikeRequest($url);
        }
    }

    /**
     * Perform a network request using the Rehike network library.
     * 
     * This is used within Rehike itself.
     */
    protected static function rehikeRequest(string $url): string
    {
        $p = NetworkCore::request($url);

        do
        {
            NetworkCore::run();
        }
        while (PromiseStatus::PENDING == $p->status);

        return $p->result;
    }

    /**
     * Perform a network request using CURL.
     * 
     * This is used as a fallback when the Rehike network library is not
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
     *    - rehike (for the Rehike network library)
     *    - curl (for cURL)
     */
    protected static function determineMode(): void
    {
        if (self::rehikeNetAvailable())
        {
            self::$mode = "rehike";
        }
        else
        {
            self::$mode = "curl";
        }
    }

    /**
     * Check the availability of the Rehike network library.
     */
    protected static function rehikeNetAvailable(): bool
    {
        return class_exists(self::RH_NETWORK_LIBRARY);
    }
}

Network::init();