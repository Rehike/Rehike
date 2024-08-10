<?php
namespace Rehike\SignInV2\Info;

use Rehike\SignInV2\Cache\ICacheable;
use Rehike\SignInV2\Builder\GoogleAccountInfoBuilder;
use Rehike\SignInV2\Cache\CacheReader;

/**
 * Used to store and retrieve information about a Google Account.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GoogleAccountInfo implements IBuiltObject, ICacheable
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

    public static function fromBuilder(GoogleAccountInfoBuilder $builder): self
    {
        $me = new self();
        
        $me->displayName = $builder->displayName;
        $me->gaiaId = $builder->gaiaId;
        $me->authUserId = $builder->authUserId;
        $me->accountEmail = $builder->accountEmail;
        $me->avatarUrl = $builder->avatarUrl;
        
        $me->assertComplete();
        
        return $me;
    }
    
    public function restoreFromCache(CacheReader $cache): void
    {
        $this->displayName = $cache->props->displayName;
        $this->gaiaId = $cache->props->gaiaId;
        $this->authUserId = $cache->props->authUserId;
        $this->accountEmail = $cache->props->accountEmail;
        $this->avatarUrl = $cache->props->avatarUrl;
        
        $this->assertComplete();
    }
    
    private function assertComplete(): void
    {
        assert(
            isset($this->displayName) &&
            isset($this->gaiaId) &&
            isset($this->authUserId) &&
            isset($this->accountEmail) &&
            isset($this->avatarUrl)
        );
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