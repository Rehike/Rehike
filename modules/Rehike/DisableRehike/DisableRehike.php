<?php
namespace Rehike\DisableRehike;

use Rehike\{
    ConfigManager\Config,
    FileSystem,
    SimpleFunnel,
    i18n
};

/**
 * Allows to user to bypass Rehike without disabling the Rehike server itself.
 * 
 * @author The Rehike Maintainers
 */
class DisableRehike
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Determines if Rehike should be disabled or not.
     * 
     * Rehike is disabled if any of the following conditions apply:
     *   - The configuration property "hidden.disableRehike" is set to true.
     *   - The URL contains a true ?enable_polymer value.
     * 
     * Rehike is enabled if any of the following conditions apply:
     *   - The configuration property is false.
     *   - The URL contains a false ?enable_polymer value.
     *   - The page is a Rehike-specific page (/rehike/ URL).
     *   - The URL contains a true ?enable_rehike value.
     */
    public static function shouldDisable(): bool
    {
        if (self::shouldPersistentlyEnableRehikeFromCurrentUrl())
            return false;

        $cfg = Config::getConfigProp("hidden.disableRehike");
        $url = self::isRehikeUrl() ? false : self::getEnablePolymerUrlState();

        // ?enable_polymer=0, false with hidden.disableRehike should actually
        // enable Rehike for that session only:
        if ($cfg == true && $url === false)
        {
            return false;
        }

        return $cfg == true || $url == true;
    }

    /**
     * Passes traffic for this session through to Polymer.
     */
    public static function disableForSession(): void
    {
        PolymerDocument::getPolymerDocument()->then(function(PolymerDocument $doc) {
            http_response_code($doc->status);

            foreach (SimpleFunnel::responseHeadersToHttp($doc->headers) as $httpHeader)
            {
                header($httpHeader, false);
            }

            echo $doc->response;
        });
    }

    /**
     * Determines if the current URL requests to enable Rehike persistently.
     */
    public static function shouldPersistentlyEnableRehikeFromCurrentUrl(): bool
    {
        if (isset($_GET["enable_rehike"]))
        {
            $er = $_GET["enable_rehike"];

            return $er == "1" || strtolower($er) == "true";
        }
        
        return false;
    }

    /**
     * Persistently enables Rehike.
     */
    public static function enableRehike(): void
    {
        Config::setConfigProp("hidden.disableRehike", false);
        Config::dumpConfig();
    }

    /**
     * Persistently disables Rehike.
     */
    public static function disableRehike(): void
    {
        Config::setConfigProp("hidden.disableRehike", true);
        Config::dumpConfig();
    }

    /**
     * Determines if the current URL requests to use Polymer instead of Rehike.
     */
    private static function isEnablePolymerUrl(): bool
    {
        return isset($_GET["enable_polymer"]);
    }

    /**
     * Determines if the current URL is a Rehike-specific page.
     */
    private static function isRehikeUrl(): bool
    {
        return strpos($_SERVER["REQUEST_URI"], "/rehike/") === 0;
    }

    /**
     * Gets the ?enable_polymer statement.
     * 
     * If the value of the parameter is truthy (1 or true), then this will
     * return true. Else, it will return false.
     * 
     * If the current URL is not an ?enable_polymer URL, then this will return
     * null.
     */
    private static function getEnablePolymerUrlState(): ?bool
    {
        if (self::isEnablePolymerUrl())
        {
            $ep = $_GET["enable_polymer"];
            return $ep == "1" || strtolower($ep) == "true";
        }

        return null;
    }
}