<?php
namespace Rehike\Signin;

/**
 * Implements the Rehike Signin API.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class API
{
    public static function isSignedIn()
    {
        return AuthManager::$isSignedIn;
    }

    public static function getInfo()
    {
        return AuthManager::$info;
    }
}