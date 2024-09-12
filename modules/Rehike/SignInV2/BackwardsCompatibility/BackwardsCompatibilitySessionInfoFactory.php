<?php
namespace Rehike\SignInV2\BackwardsCompatibility;

use Rehike\SignInV2\{
    Builder\IBuilder,
    Builder\GoogleAccountInfoBuilder,
    Builder\SessionInfoBuilder,
    Builder\YtChannelAccountInfoBuilder,
    Info\IBuiltObject,
    Info\GoogleAccountInfo,
    Info\SessionInfo,
};

use Rehike\Signin\API as LegacySigninApi;
use Rehike\Signin\AuthManager as LegacyAuthManager;

/**
 * Provides backward compatibility for interacting with the old SignInV1
 * backend with the V2 API.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class BackwardsCompatibilitySessionInfoFactory implements IBuilder
{
    private SessionInfoBuilder $builder;
    private SessionInfo $cachedSessionInfo;
    
    public function __construct()
    {
        $this->builder = new SessionInfoBuilder();
    }
    
    public function build(): SessionInfo
    {
        if (isset($this->cachedSessionInfo))
        {
            return $this->cachedSessionInfo;
        }
        
        $legacyInfo = LegacySigninApi::getInfo();
        
        $googleInfo = new GoogleAccountInfoBuilder($this->builder);
        
        // V1 doesn't support authuser
        $googleInfo->authUserId = 0;
        
        $this->builder->isSignedIn = LegacySigninApi::isSignedIn();
        
        if (isset($legacyInfo["datasyncId"]))
            $this->builder->datasyncId = $legacyInfo["datasyncId"];
        
        if (isset($legacyInfo["googleAccount"]["email"]))
            $googleInfo->accountEmail = $legacyInfo["googleAccount"]["email"];
        
        if (isset($legacyInfo["googleAccount"]["name"]))
            $googleInfo->displayName = $legacyInfo["googleAccount"]["name"];
        
        $googleInfo->isActive = true;
        
        foreach ($legacyInfo["channelPicker"] as $legacyChannel)
        {
            $channelInfo = new YtChannelAccountInfoBuilder($googleInfo);
            
            $channelInfo->isActive = $legacyChannel["selected"] ?? false;
            
            if ($channelInfo->isActive)
            {
                if (isset($legacyInfo["ucid"]))
                {
                    $channelInfo->ucid = $legacyInfo["ucid"];
                }
            }
            
            if (isset($legacyChannel["name"]))
                $channelInfo->displayName = $legacyChannel["name"];
            
            if (isset($legacyChannel["photo"]))
                $channelInfo->avatarUrl = $legacyChannel["photo"];
            
            if (isset($legacyChannel["byline"]))
                $channelInfo->localizedSubscriberCount = $legacyChannel["byline"];
            
            if (isset($legacyChannel["gaiaId"]))
                $channelInfo->gaiaId = $legacyChannel["gaiaId"];
            
            if (isset($legacyChannel["hasChannel"]))
                $channelInfo->hasChannel = $legacyChannel["hasChannel"];
        }
        
        $this->cachedSessionInfo = $this->builder->build();
        return $this->cachedSessionInfo;
    }
}