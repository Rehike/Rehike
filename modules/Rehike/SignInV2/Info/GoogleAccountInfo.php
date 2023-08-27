<?php
namespace Rehike\SignInV2\Info;

/**
 * Used to store and retrieve information about a Google Account.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GoogleAccountInfo
{
    /**
     * The public display name of the account, used to identify the account
     * to the user.
     * 
     * This can be unavailable, such as in the case of multiple Google
     * Accounts logged into at the same time, because YouTube only reports
     * the display name for the active account.
     */
    protected ?string $displayName = null;

    /**
     * The internal GAIA ID of the account, used to identify the account
     * within Google's services.
     */
    protected string $gaiaId;

    /**
     * The user session index for this account, used when multiple Google Accounts are
     * accessible.
     */
    protected int $authUserId;

    /**
     * The email address associated with this Google Account.
     */
    private string $accountEmail;

    /**
     * A URL linking to the account's profile picture.
     */
    protected string $avatarUrl;

    public function __construct(
            ?string $displayName = null,
            string $gaiaId,
            int $authUserId,
            string $accountEmail,
            string $avatarUrl
    )
    {
        $this->displayName = $displayName;
        $this->gaiaId = $gaiaId;
        $this->authUserId = $authUserId;
        $this->accountEmail = $accountEmail;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * Get the public display name of the account, used to identify the account
     * to the user.
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * Get the user session index for this account, used when multiple Google 
     * Accounts are accessible.
     */
    public function getAuthUserId(): int
    {
        return $this->authUserId;
    }

    /**
     * Get the internal GAIA ID of the account, used to identify the account
     * within Google's services.
     */
    public function getGaiaId(): string
    {
        return $this->gaiaId;
    }

    /**
     * Get the email address associated with this Google Account.
     */
    public function getAccountEmail(): string
    {
        return $this->accountEmail;
    }

    /**
     * Get a URL linking to the account's profile picture.
     */
    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }
}