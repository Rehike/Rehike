<?php
namespace Rehike\SignInV2\Info;

/**
 * Provides information about the current user session. This is the top-most
 * class for accessing data about the user session in the PHP world.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfo
{
    /**
     * States whether or not the user is signed into a valid session.
     */
    protected bool $isSignedIn = false;

    /**
     * Stores information about the currently signed in Google Account.
     * 
     * TODO(ev): SessionInfo doesn't account for multiple Google Accounts at
     * all, which is a major issue. This MUST be added at some point!!!!
     */
    protected GoogleAccountInfo $googleAccount;

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
        GoogleAccountInfo $googleAccount = null,
        YtChannelAccountInfo $activeChannel = null,
        array $otherAccounts = [],
        array $sessionErrors = []
    )
    {
        $this->isSignedIn = $isSignedIn && $googleAccount != null;

        if ($googleAccount != null)
            $this->googleAccount = $googleAccount;

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
     * Gets information about the currently signed in Google Account.
     */
    public function getGoogleAccount(): GoogleAccountInfo
    {
        return $this->googleAccount;
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