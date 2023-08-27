<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\SignInV2\Info\GoogleAccountInfo;

/**
 * Builder for the YtChannelAccountInfo class.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class YtChannelAccountInfoBuilder
{
    private GoogleAccountInfoBuilder $parent;

    private GoogleAccountInfoBuilder $ownerAccount;
    private string $ucid;

    public function __construct(GoogleAccountInfoBuilder $parent)
    {
        $this->parent = $parent;
        $this->setOwnerAccount($parent);
    }

    public function build(): YtChannelAccountInfo
    {
        return new YtChannelAccountInfo();
    }

    public function getOwnerAccount(): GoogleAccountInfoBuilder
    {
        return $this->ownerAccount;
    }

    public function setOwnerAccount(GoogleAccountInfoBuilder $instance): void
    {
        $this->ownerAccount = $instance;
    }

    public function getUcid(): string
    {
        return $this->ucid;
    }

    public function setUcid(string $newValue): void
    {
        $this->ucid = $newValue;
    }
}