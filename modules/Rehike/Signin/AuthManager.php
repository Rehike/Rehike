<?php
namespace Rehike\Signin;

use \YukisCoffee\CoffeeRequest\CoffeeRequest;
use \Rehike\Request;
use Rehike\FileSystem as FS;

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
     * @var bool
     */
    private static $shouldAuth = false;

    /**
     * Stores the user's SAPISID; a login cookie used to authenticate each
     * individual request.
     * 
     * During the authentication process, this is hashed, resulting in an
     * SAPISIDHASH value.
     * 
     * @var string
     */
    private static $sapisid;

    /**
     * Stores the current account's GAIA ID.
     * 
     * This is used internally in some places, as well as is used to identify
     * the Google account as a whole.
     * 
     * @var string
     */
    private static $currentGaiaId = "";

    /**
     * Stores the current signin state.
     * 
     * This isn't guaranteed. If an error occurs while attempting to get
     * authentication data, the signin request will be rejected and this will
     * remain false.
     * 
     * @var bool
     */
    public static $isSignedIn = false;

    /**
     * Stores the resulting information from a signin request.
     * 
     * @var ?array
     */
    public static $info = null;

    public static function __initStatic()
    {
        self::init();
    }

    public static function init()
    {
        self::$shouldAuth = self::determineShouldAuth();
    }

    /**
     * Provide the global context ($yt) for internal use.
     * 
     * @param object $yt Global context
     * @return void
     */
    public static function use(&$yt)
    {
        // Merge data into main variable sent to the
        // templater and whatnot.
        // Also tell the request manager to use this too.

        if (self::shouldAuth())
        {
            Request::useAuth();

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
     * 
     * @return bool
     */
    public static function shouldAuth()
    {
        return self::$shouldAuth;
    }

    /**
     * Used internally as a one-time determination of the authentication's
     * availability.
     * 
     * @return bool
     */
    private static function determineShouldAuth()
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
     * 
     * @param string $origin
     * @return string
     */
    public static function getAuthHeader($origin = "https://www.youtube.com")
    {
        $sapisid = self::$sapisid;
        $time = time();
        $sha1 = sha1("{$time} {$sapisid} {$origin}");
        return "SAPISIDHASH {$time}_{$sha1}";
    }

    /**
     * Get the current user's GAIA ID.
     * 
     * @return string
     */
    public static function getGaiaId()
    {
        return self::$currentGaiaId;
    }

    /**
     * Retrieve signin data from the server or cache.
     * 
     * @return array
     */
    public static function getSigninData()
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

                Request::authUseGaiaId();

                self::processMenuData(self::$info, $data->menu);

                return self::$info;
            }
        }

        // This is the fallback in all other cases.
        self::$info = self::requestSigninData();
        return self::$info;
    }

    /**
     * Request signin data from the server.
     * 
     * @return array
     */
    public static function requestSigninData()
    {
        // Temporarily switch the request namespace
        $previousNamespace = Request::getNamespace();

        // Perform the necessary request
        Request::setNamespace("rehike.signin_temp_ns");

        // These must be separate in order to account for GAIA id.
        Request::queueUrlRequest("switcher", "https://www.youtube.com/getAccountSwitcherEndpoint");
        $switcher = Request::getResponses()["switcher"];
        
        $info = self::processSwitcherData($switcher);
        
        Request::authUseGaiaId();
        
        // Then the account menu request can work
        // also hack i can't be fucked to fix the other code
        Request::queueInnertubeRequest("menu", "account/account_menu", (object)[
            "deviceTheme" => "DEVICE_THEME_SUPPORTED",
            "userInterfaceTheme" => "USER_INTERFACE_THEME_DARK"
        ]);
        $menu = Request::getResponses()["menu"];

        // Reset the request namespace now that I'm done!
        Request::setNamespace($previousNamespace);

        self::processMenuData($info, $menu);

        $responses = [
            "switcher" => &$switcher,
            "menu" => &$menu
        ];

        Cacher::writeCache($responses);

        return $info;
    }

    /**
     * Generate a new signin data array from the getAccountSwitcherEndpoint
     * response.
     * 
     * @param string $switcher getAccountSwitcherEndpoint response
     * @return array
     */
    public static function processSwitcherData($switcher)
    {
        $info = Switcher::parseResponse($switcher);

        self::$currentGaiaId = &$info["activeChannel"]["gaiaId"];

        // Since no errors were thrown, assume everything
        // works.
        self::$isSignedIn = true;

        return $info;
    }

    /**
     * Modify the switcher endpoint's data to add the UCID obtained from the
     * menu data.
     * 
     * @param array $info Reference to the data to modify.
     * @param string $menu Response of the account_menu endpoint.
     */
    public static function processMenuData(&$info, $menu)
    {
        // UCID must be retrieved here to work with GAIA id
        $info["ucid"] = self::getUcid(json_decode($menu));
    }

    /**
     * Get the UCID of the active channel.
     * 
     * @param object $menu
     * @return string
     */
    public static function getUcid($menu)
    {
        if ($items = @$menu->actions[0]->openPopupAction->popup
            ->multiPageMenuRenderer->sections[0]->multiPageMenuSectionRenderer
            ->items
        )
        {
            foreach ($items as $item)
            {
                $item = @$item->compactLinkRenderer;

                if ("ACCOUNT_BOX" == @$item->icon->iconType)
                {
                    return $item->navigationEndpoint->browseEndpoint->browseId ?? null;
                }
            }
        }
        
        return "";
    }

    /**
     * Get the unique session cookie (if it exists)
     * 
     * @return string (even "null" as a string)
     */
    public static function getUniqueSessionCookie()
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