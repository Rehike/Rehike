<?php
namespace Rehike\SignInV2\Info;

/**
 * Used to store and retrieve information about a YouTube channel.
 * 
 * Since YouTube channels are internally based on the otherwise deprecated
 * Brand Account system, they share many similarities with full-on
 * Google Accounts and are implemented as a subclass as a result.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class YtChannelAccountInfo extends GoogleAccountInfo
{
    /**
     * The Google Account which owns the channel's Brand Account, or which
     * is used to access it.
     */
    protected GoogleAccountInfo $ownerAccount;

    /**
     * The unique identifier used to identify the user channel by YouTube.
     */
    protected string $ucid;

    public function __construct(
        GoogleAccountInfo $ownerAccount,
        string $displayName,
        string $gaiaId,
        string $ucid,
        string $avatarUrl
    )
    {
        $this->ownerAccount = $ownerAccount;
        $this->gaiaId = $gaiaId;
        $this->ucid = $ucid;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * Get the email address of the account which is used to access this
     * channel.
     */
    public function getAccountEmail(): string
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

    /**
     * Get the unique identifier used to identify the user channel by YouTube.
     */
    public function getUcid(): string
    {
        return $this->ucid;
    }
}