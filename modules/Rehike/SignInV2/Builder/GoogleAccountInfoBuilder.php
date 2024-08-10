<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\GoogleAccountInfo;

/**
 * Builder for the GoogleAccountInfo class.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
final class GoogleAccountInfoBuilder implements IBuilder
{
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
    public string $gaiaId;

    /**
     * The user session index for this account, used when multiple Google Accounts are
     * accessible.
     */
    public int $authUserId;

    /**
     * The email address associated with this Google Account.
     */
    public string $accountEmail;

    /**
     * A URL linking to the account's profile picture.
     */
    public string $avatarUrl;
    
    private SessionInfoBuilder $parent;
    
    private array $ytChannels = [];

    public function __construct(SessionInfoBuilder $parent)
    {
        $this->parent = $parent;
    }

    public function build(): GoogleAccountInfo
    {
        return new GoogleAccountInfo($this);
    }

    public function insertYtChannel(): YtChannelAccountInfoBuilder
    {
        $instance = new YtChannelAccountInfoBuilder($this);
        $this->ytChannels[] = $instance;
        return $instance;
    }
}