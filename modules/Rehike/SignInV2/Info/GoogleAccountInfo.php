<?php
namespace Rehike\SignInV2\Info;

use Rehike\SignInV2\Cache\ICacheable;
use Rehike\SignInV2\Builder\GoogleAccountInfoBuilder;
use Rehike\SignInV2\Builder\YtChannelAccountInfoBuilder;
use Rehike\SignInV2\Cache\AutoCacheable;
use Rehike\SignInV2\Cache\CacheReader;

/**
 * Used to store and retrieve information about a Google Account.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GoogleAccountInfo extends GoogleAccountInfoBase implements IBuiltObject, ICacheable
{
    use AutoCacheable;
    
    /**
     * @var YtChannelAccountInfo[]
     */
    protected array $youtubeChannels = [];

    public function __construct(GoogleAccountInfoBuilder $builder)
    {
        $this->displayName = $builder->displayName;
        $this->gaiaId = $builder->gaiaId;
        $this->authUserId = $builder->authUserId;
        $this->accountEmail = $builder->accountEmail;
        $this->avatarUrl = $builder->avatarUrl;
        $this->isActive = $builder->isActive;
        
        $this->youtubeChannels = $builder->ytChannels
            ->forEach(fn(YtChannelAccountInfoBuilder $item) => $item->setFinalizedParent($this))
            ->buildAll();
    }

    /**
     * @return YtChannelAccountInfo[]
     */
    public function getYoutubeChannels(): array
    {
        return $this->youtubeChannels;
    }
}