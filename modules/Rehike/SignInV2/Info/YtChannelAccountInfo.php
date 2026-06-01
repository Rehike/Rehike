<?php
namespace Rehike\SignInV2\Info;

use Rehike\SignInV2\Builder\YtChannelAccountInfoBuilder;
use Rehike\SignInV2\Cache\AutoCacheable;
use Rehike\SignInV2\Cache\ICacheable;

/**
 * Used to store and retrieve information about a YouTube channel.
 * 
 * Since YouTube channels are internally based on the otherwise deprecated
 * Brand Account system, they share many similarities with full-on
 * Google Accounts and are implemented as a subclass of the base class
 * as a result.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class YtChannelAccountInfo extends GoogleAccountInfoBase implements IBuiltObject
{
    /**
     * The Google Account which owns the channel's Brand Account, or which
     * is used to access it.
     */
    protected GoogleAccountInfo $ownerAccount;
    
    protected bool $hasChannel = false;
    
    protected bool $isDefaultChannel = false;

    /**
     * The unique identifier used to identify the user channel by YouTube.
     */
    protected ?string $ucid = null;
    
    /**
     * 
     */
    protected ?string $localizedSubscriberCount = null;

    public function __construct(YtChannelAccountInfoBuilder $builder)
    {
        $this->ownerAccount = $builder->getFinalizedParent();
        
        $this->displayName = $builder->displayName;
        $this->gaiaId = $builder->gaiaId;
        $this->accountEmail = $builder->accountEmail;
        $this->avatarUrl = $builder->avatarUrl;
        $this->ucid = $builder->ucid;
        $this->hasChannel = $builder->hasChannel;
        $this->localizedSubscriberCount = $builder->localizedSubscriberCount;
        $this->isActive = $builder->isActive;
        $this->isDefaultChannel=  $builder->isDefaultChannel;
    }
    
    /**
     * @override
     */
    public function getAuthUserId(): ?int
    {
        return $this->getOwnerAccount()->getAuthUserId();
    }

    /**
     * Get the email address of the account which is used to access this
     * channel.
     */
    public function getAccountEmail(): ?string
    {
        return $this->ownerAccount->getAccountEmail();
    }

    /**
     * Get the Google Account which owns the channel's Brand Account, or which
     * is used to access it.
     */
    public function getOwnerAccount(): GoogleAccountInfo
    {
        return $this->ownerAccount;
    }
    
    public function getHasChannel(): bool
    {
        return $this->hasChannel;
    }
    
    public function getIsDefaultChannel(): bool
    {
        return $this->isDefaultChannel;
    }

    /**
     * Get the unique identifier used to identify the user channel by YouTube.
     */
    public function getUcid(): ?string
    {
        return $this->ucid;
    }
    
    /**
     * 
     */
    public function getSwitchUrl(?string $nextUrl = null, bool $alwaysSkipIdentityPrompt = false): ?string
    {
        $nextSegment = "";
        
        if ($nextUrl)
        {
            $nextSegment = "&next=" . urlencode($nextUrl);
        }
        
        $skipIdentityPromptSegment = "";
        
        if ($alwaysSkipIdentityPrompt || $this->getOwnerAccount()->isActive())
        {
            $skipIdentityPromptSegment = "&skip_identity_prompt=true";
        }
        
        $pageIdIsSameAsGoogleAccount = $this->getGaiaId() == $this->getOwnerAccount()->getGaiaId();
        
        return "/signin?action_handle_signin=true" .
            "&authuser=" . urlencode($this->getAuthUserId() ?? "0") .
            (
                $pageIdIsSameAsGoogleAccount
                    ? ""
                    : "&pageid=" . urlencode($this->getGaiaId() ?? "0")
            ).
            $nextSegment .
            $skipIdentityPromptSegment;
    }
    
    /**
     * 
     */
    public function getLocalizedSubscriberCount(): ?string
    {
        return $this->localizedSubscriberCount;
    }
}