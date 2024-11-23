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
    /**
     * Check if the current session is signed in.
     */
    public static function isSignedIn(): bool
    {
        return AuthManager::$isSignedIn;
    }

    /**
     * Get account information from the private AuthManager.
     */
    public static function getInfo(): ?array
    {
        return AuthManager::$info;
    }
}