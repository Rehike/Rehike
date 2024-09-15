<?php
namespace Rehike\SignInV2\Info;

/**
 * Used to store and retrieve information about a Google Account.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class GoogleAccountInfoBase
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
    protected ?string $gaiaId;

    /**
     * The email address associated with this Google Account.
     */
    protected ?string $accountEmail;

    /**
     * A URL linking to the account's profile picture.
     */
    protected ?string $avatarUrl;
    
    protected bool $isActive = false;

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
    abstract public function getAuthUserId(): ?int;

    /**
     * Get the internal GAIA ID of the account, used to identify the account
     * within Google's services.
     */
    public function getGaiaId(): ?string
    {
        return $this->gaiaId;
    }

    /**
     * Get the email address associated with this Google Account.
     */
    public function getAccountEmail(): ?string
    {
        return $this->accountEmail;
    }

    /**
     * Get a URL linking to the account's profile picture.
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
}