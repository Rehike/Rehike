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
    const CACHE_FILE = "cache/signin_cache.json";

    public static $shouldAuth = false;
    private static $sapisid;
    private static $currentGaiaId = "";

    // Just because it should be the case doesn't
    // mean that it will always be.
    // If an error occurs during this process,
    // this will remain false.
    public static $isSignedIn = false;

    public static $info = null;

    public static function __initStatic()
    {
        self::init();
    }

    public static function init()
    {
        self::$shouldAuth = self::determineShouldAuth();
    }

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

    public static function shouldAuth()
    {
        return self::$shouldAuth;
    }

    public static function determineShouldAuth()
    {
        // Determined by the presence of SAPISID cookie.
        if (isset($_COOKIE) && isset($_COOKIE["SAPISID"]))
        {
            self::$sapisid = $_COOKIE["SAPISID"];
            return true;
        }

        return false;
    }

    public static function getAuthHeader($origin = "https://www.youtube.com")
    {
        $sapisid = self::$sapisid;
        $time = time();
        $sha1 = sha1("{$time} {$sapisid} {$origin}");
        return "SAPISIDHASH {$time}_{$sha1}";
    }

    public static function getGaiaId()
    {
        return self::$currentGaiaId;
    }

    public static function getSigninData()
    {
        if (null != self::$info)
        {
            return self::$info;
        }
        else if ($cache = self::getCache())
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

        self::writeCache($responses);

        return $info;
    }

    public static function processSwitcherData($switcher)
    {
        $info = Switcher::parseResponse($switcher);

        self::$currentGaiaId = &$info["activeChannel"]["gaiaId"];

        // Since no errors were thrown, assume everything
        // works.
        self::$isSignedIn = true;

        return $info;
    }

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
                    return $item->navigationEndpoint->browseEndpoint->browseId;
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

    /**
     * Get the cache if it exists.
     * 
     * @return object|false
     */
    protected static function getCache()
    {
        if (FS::fileExists(self::CACHE_FILE))
        {
            try
            {
                $json = FS::getFileContents(self::CACHE_FILE);
                
                $object = json_decode($json);

                if (time() > @$object->expire)
                    return false;

                if (null != $object)
                    return $object;
                else
                    return false;
            }
            catch (\Throwable $e)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Attempt to write the cache to a file.
     * 
     * @return void
     */
    protected static function writeCache($responses, $noCheck = false)
    {
        if (!FS::fileExists(self::CACHE_FILE) && !$noCheck)
        {
            $data = (object)[
                "expire" => time() + 10080, // 1 week
                "responseCache" => (object)[
                    self::getUniqueSessionCookie()
                        => (object)$responses
                ]
            ];
            
            FS::writeFile(self::CACHE_FILE, json_encode($data));
        }
        else
        {
            return self::updateCache($responses);
        }
    }

    /**
     * Update a pre-existing cache file.
     * 
     * @return void
     */
    protected static function updateCache($responses)
    {
        $data = json_decode(FS::getFileContents(self::CACHE_FILE));

        // Skip if invalid
        if (false == $data) return self::writeCache($responses, true);

        $sessionId = self::getUniqueSessionCookie();

        @$data->expire += 1440; // 1 day
        @$data->responseCache->{$sessionId} = $responses;

        FS::writeFile(self::CACHE_FILE, json_encode($data));
    }
}