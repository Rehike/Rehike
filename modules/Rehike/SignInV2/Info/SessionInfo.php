<?php
namespace Rehike\SignInV2\Info;

use Rehike\SignInV2\Builder\SessionInfoBuilder;
use Rehike\SignInV2\Cache\AutoCacheable;
use Rehike\SignInV2\Cache\CacheProperty;
use Rehike\SignInV2\Cache\ICacheable;
use Rehike\SignInV2\SessionErrors;

/**
 * Provides information about the current user session. This is the top-most
 * class for accessing data about the user session in the PHP world.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class SessionInfo implements IBuiltObject, ICacheable
{
    use AutoCacheable;
    
    /**
     * States whether or not the user is signed into a valid session.
     */
    protected bool $isSignedIn = false;

    /**
     * Stores information about all accessible Google Accounts.
     */
    protected array $googleAccounts = [];
    
    /**
     * 
     */
    #[CacheProperty(CacheProperty::CACHE_NEVER)]
    protected ?GoogleAccountInfo $activeGoogleAccount = null;

    /**
     * Stores information about the currently used YouTube channel.
     */
    #[CacheProperty(CacheProperty::CACHE_NEVER)]
    protected ?YtChannelAccountInfo $activeChannel = null;

    /**
     * Stores a list of other YouTube channels accessible under the current
     * Google Account.
     * 
     * @var YtChannelAccountInfo[]
     */
    #[CacheProperty(CacheProperty::CACHE_NEVER)]
    protected array $otherChannels;
    
    protected ?string $currentDatasyncId = null;

    /**
     * A bitmask that stores information about various errors that may have
     * occurred in signing in.
     * 
     * This will be 0 if there are no errors.
     */
    protected int $sessionErrors = 0;

    public function __construct(SessionInfoBuilder $builder)
    {
        $this->isSignedIn = $builder->isSignedIn && !empty($builder->googleAccounts);
        $this->currentDatasyncId = $builder->datasyncId;

        if (!empty($builder->googleAccounts))
        {
            $this->googleAccounts = $builder->googleAccounts->buildAll();
        }
            
        $this->sessionErrors = $builder->sessionErrors;
        
        // Precache these references:
        $this->getCurrentGoogleAccount();
        $this->getActiveChannel();
        $this->getOtherChannels();
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
     * 
     * @return GoogleAccountInfo[]
     */
    public function getGoogleAccounts(): array
    {
        return $this->googleAccounts;
    }

    /**
     * Gets information about the currently signed in Google Account.
     */
    public function getCurrentGoogleAccount(): ?GoogleAccountInfo
    {
        if (!is_null($this->activeGoogleAccount))
        {
            return $this->activeGoogleAccount;
        }
        
        foreach ($this->getGoogleAccounts() as $googleAccount)
        {
            if ($googleAccount->isActive())
            {
                $this->activeGoogleAccount = $googleAccount;
                break;
            }
        }
        
        return $this->activeGoogleAccount;
    }

    /**
     * Gets information about the currently used YouTube channel.
     */
    public function getActiveChannel(): ?YtChannelAccountInfo
    {
        if (!is_null($this->activeChannel))
        {
            return $this->activeChannel;
        }
        
        foreach ($this->getCurrentGoogleAccount()->getYoutubeChannels() as $channel)
        {
            if ($channel->isActive())
            {
                $this->activeChannel = $channel;
                break;
            }
        }
        
        return $this->activeChannel;
    }

    /**
     * Gets a list of other YouTube channels accessible under the current
     * Google Account.
     */
    public function getOtherChannels(): array
    {
        if (isset($this->otherChannels))
        {
            return $this->otherChannels;
        }
        
        if ($activeGoogAcc = $this->getCurrentGoogleAccount())
        {
            $this->otherChannels = $activeGoogAcc->getYoutubeChannels();
        }
        else
        {
            $this->otherChannels = [];
        }
        
        return $this->otherChannels;
    }
    
    public function getDatasyncId(): string
    {
        return $this->currentDatasyncId;
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
    
    /**
     * Checks if any request had failed.
     */
    public function didAnyRequestFail(): bool
    {
        return $this->sessionErrors & SessionErrors::FAILED_REQUEST;
    }
}