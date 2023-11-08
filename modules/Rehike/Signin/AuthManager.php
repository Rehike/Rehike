<?php
namespace Rehike\Signin;

use YukisCoffee\CoffeeRequest\CoffeeRequest;
use YukisCoffee\CoffeeRequest\Promise;
use YukisCoffee\CoffeeRequest\Network\Request;
use YukisCoffee\CoffeeRequest\Network\Response;

use Rehike\Network;
use Rehike\FileSystem as FS;
use Rehike\YtApp;
use Rehike\Util\ChannelUtils;

/**
 * Treat this as private. Use the API please.
 * 
 * This handles most of the internal behaviour. I'm too lazy
 * to clean it up.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class AuthManager
{
    /**
     * Stores the value returned by the public API method self::shouldAuth().
     * 
     * @internal
     */
    private static bool $shouldAuth = false;

    /**
     * Stores the user's SAPISID; a login cookie used to authenticate each
     * individual request.
     * 
     * During the authentication process, this is hashed, resulting in an
     * SAPISIDHASH value.
     */
    private static string $sapisid;

    /**
     * Stores the current account's GAIA ID.
     * 
     * This is used internally in some places, as well as is used to identify
     * the Google account as a whole.
     */
    private static string $currentGaiaId = "";

    /**
     * Stores the current signin state.
     * 
     * This isn't guaranteed. If an error occurs while attempting to get
     * authentication data, the signin request will be rejected and this will
     * remain false.
     */
    public static bool $isSignedIn = false;

    /**
     * Stores the resulting information from a signin request.
     */
    public static ?array $info = null;

    public static function __initStatic()
    {
        self::init();
    }

    public static function init(): void
    {
        self::$shouldAuth = self::determineShouldAuth();
    }

    /**
     * Provide the global context ($yt) for internal use.
     */
    public static function use(YtApp $yt): void
    {
        // Merge data into main variable sent to the
        // templater and whatnot.
        // Also tell the request manager to use this too.

        if (self::shouldAuth())
        {
            Network::useAuthService();

            $data = self::getSigninData();

            if (self::$isSignedIn)
            {
                $yt->signin = ["isSignedIn" => true] + $data;
                return;
            }
        }
        $yt->signin = ["isSignedIn" => false];
    }

    /**
     * Get if authentication is available.
     */
    public static function shouldAuth(): bool
    {
        return self::$shouldAuth;
    }

    /**
     * Used internally as a one-time determination of the authentication's
     * availability.
     */
    private static function determineShouldAuth(): bool
    {
        // Determined by the presence of SAPISID cookie.
        if (isset($_COOKIE) && isset($_COOKIE["SAPISID"]))
        {
            self::$sapisid = $_COOKIE["SAPISID"];
            return true;
        }

        return false;
    }

    /**
     * Get the contents of the authentication HTTP header. This will generate
     * a new SAPISIDHASH.
     */
    public static function getAuthHeader(string $origin = "https://www.youtube.com"): string
    {
        $sapisid = self::$sapisid;
        $time = time();
        $sha1 = sha1("{$time} {$sapisid} {$origin}");
        return "SAPISIDHASH {$time}_{$sha1}";
    }

    /**
     * Get the current user's GAIA ID.
     */
    public static function getGaiaId(): string
    {
        return self::$currentGaiaId;
    }

    /**
     * Retrieve signin data from the server or cache.
     */
    public static function getSigninData(): array
    {
        if (null != self::$info)
        {
            return self::$info;
        }
        else if ($cache = Cacher::getCache())
        {
            $sessionId = self::getUniqueSessionCookie();

            if ($data = @$cache->responseCache->{$sessionId})
            {
                self::$info = self::processSwitcherData(
                    $data->switcher
                );
                self::$info["ucid"] = $data->ucid;

                Network::useAuthGaiaId();

                return self::$info;
            }
        }

        // This is the fallback in all other cases.
        self::$info = self::requestSigninData();
        return self::$info;
    }

    /**
     * Request signin data from the server.
     */
    public static function requestSigninData(): array
    {
        /** @var string */
        $switcher = null;
        /** @var string */
        $ucid = null;

        /** @var array */
        $info = null;
        
        Network::urlRequest(
            "https://www.youtube.com/getAccountSwitcherEndpoint",
            Network::getDefaultYoutubeOpts()
        )->then(function($response) use (&$switcher, &$info) {
            $switcher = $response->getText();

            $info = self::processSwitcherData($switcher);

            Network::useAuthGaiaId();

            return Network::innertubeRequest(
                action: "navigation/resolve_url",
                body: [
                    "url" => "https://www.youtube.com/profile"
                ]
            );
        })->then(function($response) use (&$menu) {
            $response = $response->getJson();

            $endpoint = $response->endpoint->urlEndpoint->url;

            if (strpos($endpoint, "/channel/") !== false)
            {
                $ucid = explode("/channel/", $endpoint)[1];

                return new Promise(function($r) use ($ucid) {
                    $r($ucid);
                });
            }
            else
            {
                return new Promise(function($r) use ($endpoint) {
                    ChannelUtils::getUcid($endpoint)->then(function ($ucid) use ($r) {
                        $r($ucid);
                    });
                });
            }
        })->then(function($ucidValue) use (&$ucid) {
            $ucid = $ucidValue;
        });

        // This call blocks the thread until the requests are done.
        Network::run();

        $responses = [
            "switcher" => &$switcher,
            "ucid" => &$ucid
        ];

        $info["ucid"] = $ucid;

        Cacher::writeCache($responses);

        return $info;
    }

    /**
     * Generate a new signin data array from the getAccountSwitcherEndpoint
     * response.
     * 
     * @param $switcher getAccountSwitcherEndpoint response
     */
    public static function processSwitcherData(string $switcher): array
    {
        $info = Switcher::parseResponse($switcher);

        self::$currentGaiaId = &$info["activeChannel"]["gaiaId"];

        // Since no errors were thrown, assume everything
        // works.
        self::$isSignedIn = true;

        return $info;
    }

    /**
     * Get the unique session cookie (if it exists)
     * 
     * @return string (even "null" as a string)
     */
    public static function getUniqueSessionCookie(): string
    {
        if (isset($_COOKIE["LOGIN_INFO"]))
        {
            return $_COOKIE["LOGIN_INFO"];
        }
        else
        {
            return "null";
        }
    }
}