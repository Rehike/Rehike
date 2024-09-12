<?php
namespace Rehike\SignInV2\Builder;

use InvalidArgumentException;
use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\SignInV2\Info\GoogleAccountInfo;
use Rehike\SignInV2\Info\IBuiltObject;

/**
 * Builder for the YtChannelAccountInfo class.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class YtChannelAccountInfoBuilder implements IBuilder, IBuilderWithParent
{
    public GoogleAccountInfo $finalParent;
    
    public GoogleAccountInfoBuilder $parentBuilder;
    
    /**
     * The public display name of the account, used to identify the account
     * to the user.
     * 
     * This can be unavailable, such as in the case of multiple Google
     * Accounts logged into at the same time, because YouTube only reports
     * the display name for the active account.
     */
    public ?string $displayName = null;

    /**
     * The internal GAIA ID of the account, used to identify the account
     * within Google's services.
     */
    public ?string $gaiaId = null;

    /**
     * The user session index for this account, used when multiple Google Accounts are
     * accessible.
     */
    public ?int $authUserId = null;

    /**
     * The email address associated with this Google Account.
     */
    public ?string $accountEmail = null;

    /**
     * A URL linking to the account's profile picture.
     */
    public ?string $avatarUrl = null;
    
    public bool $hasChannel = false;
    
    /**
     * 
     */
    public bool $isActive = false;
    
    /**
     * 
     */
    public ?string $ucid = null;
    
    public ?string $localizedSubscriberCount = null;

    public function __construct(GoogleAccountInfoBuilder $parentBuilder)
    {
        $this->parentBuilder = $parentBuilder;
        $parentBuilder->ytChannels->push($this);
    }
    
    public function getFinalizedParent(): GoogleAccountInfo
    {
        return $this->finalParent;
    }
    
    public function setFinalizedParent(IBuiltObject $parent): void
    {
        if (!($parent instanceof GoogleAccountInfo))
        {
            throw new InvalidArgumentException("Expected " . GoogleAccountInfo::class);
        }
        
        $this->finalParent = $parent;
    }

    public function build(): YtChannelAccountInfo
    {
        return new YtChannelAccountInfo($this);
    }
}