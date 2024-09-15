<?php
namespace Rehike\SignInV2;

use Rehike\SignInV2\Info\SessionInfo;

use Rehike\ConfigManager\Config;

// API backwards compatibility:
const SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT = true;

use Rehike\Signin\{
    API as LegacySigninApi,
    AuthManager as LegacySigninAuthManager,
};

use Rehike\SignInV2\{
    BackwardsCompatibility\BackwardsCompatibilitySessionInfoFactory,
};

/**
 * Implements the main sign-in API.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class SignIn
{
    public static function isSignedIn(): bool
    {
        if (SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT && !self::shouldUseSV2())
        {
            return LegacySigninApi::isSignedIn();
        }
        
        return false;
    }
    
    public static function getSessionInfo(): ?SessionInfo
    {
        if (SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT && !self::shouldUseSV2())
        {
            return BackwardsCompatibilitySessionInfoFactory::getInstance()->build();
        }
        
        // temporary
        return null;
    }
    
    protected static function shouldUseSV2(): bool
    {
        if (Config::getConfigProp("experiments.useSignInV2") !== true)
        {
            return false;
        }
        
        return true;
    }
}