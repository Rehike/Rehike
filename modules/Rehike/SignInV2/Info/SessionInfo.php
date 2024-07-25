<?php
namespace Rehike\SignInV2\Info;

/**
 * Provides information about the current user session. This is the top-most
 * class for accessing data about the user session in the PHP world.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfo
{
    /**
     * States whether or not the user is signed into a valid session.
     */
    protected bool $isSignedIn = false;

    /**
     * Stores information about all accessible Google Accounts.
     */
    protected array $googleAccounts = [];

    /**
     * Stores information about the currently used YouTube channel.
     */
    protected YtChannelAccountInfo $activeChannel;

    /**
     * Stores a list of other YouTube channels accessible under the current
     * Google Account.
     * 
     * @var YtChannelAccountInfo[]
     */
    protected array $otherAccounts;

    /**
     * A bitmask that stores information about various errors that may have
     * occurred in signing in.
     * 
     * This will be 0 if there are no errors.
     */
    protected int $sessionErrors = 0;

    public function __construct(
            bool $isSignedIn,
            array $googleAccounts = null,
            YtChannelAccountInfo $activeChannel = null,
            array $otherAccounts = [],
            array $sessionErrors = []
    )
    {
        $this->isSignedIn = $isSignedIn && !empty($googleAccounts);

        if (!empty($googleAccounts))
            $this->googleAccounts = $googleAccounts;

        if ($activeChannel != null)
            $this->activeChannel = $activeChannel;

        $this->otherAccounts = $otherAccounts;
        $this->sessionErrors = $sessionErrors;
    }

    /**
     * Gets whether or not the user is signed into a valid session.
     */
    public function getIsSignedIn(): bool
    {
        return $this->isSignedIn;
    }

    /**
     * Get information about all accessible Google Accounts.
     */
    public function getGoogleAccounts(): array
    {
        return $this->googleAccounts;
    }

    /**
     * Gets information about the currently signed in Google Account.
     */
    public function getCurrentGoogleAccount(): GoogleAccountInfo
    {
        return $this->activeChannel->getOwnerAccount();
    }

    /**
     * Gets information about the currently used YouTube channel.
     */
    public function getActiveChannel(): YtChannelAccountInfo
    {
        return $this->activeChannel;
    }

    /**
     * Gets a list of other YouTube channels accessible under the current
     * Google Account.
     */
    public function getOtherAccounts(): array
    {
        return $this->otherAccounts;
    }

    /**
     * Gets a bitmask that stores information about various errors that may
     * have occurred in signing in.
     * 
     * The result will be 0 if there are no errors.
     */
    public function getSessionErrors(): int
    {
        return $this->sessionErrors;
    }
}