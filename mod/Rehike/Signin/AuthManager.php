<?php
namespace Rehike\Signin;

use \YukisCoffee\CoffeeRequest\CoffeeRequest;
use \Rehike\Request;

class AuthManager
{
    public static $shouldAuth = false;
    private static $sapisid;

    // Just because it should be the case doesn't
    // mean that it will always be.
    // If an error occurs during this process,
    // this will remain false.
    public static $isSignedIn = false;

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
            $data = self::getSigninData();

            if (self::$isSignedIn)
            {
                Request::useAuth();
                $yt->signin = (object) (["isSignedIn" => true] + $data);
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

    public static function getSigninData()
    {
        // Todo: caching!
        // At the moment, this is repeated per every new
        // session, so it adds a little bit of overhead.
        // It's not too bad, but best to eliminate it altogether.

        return self::requestSigninData();
    }

    public static function requestSigninData()
    {
        $requester = new CoffeeRequest();

        $requester->queueRequest("https://www.youtube.com/getAccountSwitcherEndpoint", [], "switcher");
        $responses = $requester->runQueue();

        $info = Switcher::parseResponse($responses["switcher"]);

        // Since no errors were thrown, assume everything
        // works.
        self::$isSignedIn = true;

        return $info;
    }
}
AuthManager::init();