<?php
namespace Rehike\SignInV2;

use Rehike\Async\Promise;
use Rehike\SignInV2\Info\SessionInfo;

use Rehike\ConfigManager\Config;
use Rehike\Network;

// API backwards compatibility:
const SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT = true;

use Rehike\Signin\{
    API as LegacySigninApi,
    AuthManager as LegacySigninAuthManager,
};

use Rehike\SignInV2\{
    BackwardsCompatibility\BackwardsCompatibilitySessionInfoFactory,
};
use Rehike\SignInV2\Info\GoogleAccountInfo;
use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\YtApp;

use function Rehike\Async\async;

/**
 * Implements the main sign-in API.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class SignIn
{    
    /**
     * Stores the GAIA authentication information for the current application
     * session.
     */
    private static ?SessionInfo $sessionInfo = null;
    
    /**
     * Ensures the initialisation of the sign in system.
     * 
     * @return Promise<void>
     */
    public static function init(): Promise/*<void>*/
    {
        return async(function()
        {
            if (!self::shouldUseSV2())
            {
                return;
            }
            
            if (!GaiaAuthManager::shouldAttemptAuth())
            {
                return;
            }
            
            Network::useAuthService2();
            self::$sessionInfo = yield GaiaAuthManager::getInfo();
        });
    }
    
    public static function isSignedIn(): bool
    {
        if (SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT && !self::shouldUseSV2())
        {
            return LegacySigninApi::isSignedIn();
        }
        
        return self::$sessionInfo != null;
    }
    
    public static function getSessionInfo(): ?SessionInfo
    {
        if (SIGNINV2_ENABLE_V1_BACKWARDS_COMPAT && !self::shouldUseSV2())
        {
            return BackwardsCompatibilitySessionInfoFactory::getInstance()->build();
        }
        
        return self::$sessionInfo;
    }
    
    public static function getCurrentGoogleAccount(): ?GoogleAccountInfo
    {
        return self::getSessionInfo()?->getCurrentGoogleAccount();
    }
    
    public static function getCurrentChannel(): ?YtChannelAccountInfo
    {
        return self::getSessionInfo()?->getCurrentChannel();
    }
    
    public static function getDatasyncId(): ?string
    {
        return self::getSessionInfo()?->getDatasyncId() ?? null;
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