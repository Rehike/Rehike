<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\SignInV2\Info\GoogleAccountInfo;

/**
 * Builder for the YtChannelAccountInfo class.
 * 
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class YtChannelAccountInfoBuilder
{
    private GoogleAccountInfo $ownerAccount;
    private string $ucid;

    public function build(): YtChannelAccountInfo
    {
        return new YtChannelAccountInfo();
    }

    public function getOwnerAccount(): GoogleAccountInfo
    {
        return $this->ownerAccount;
    }

    public function setOwnerAccount(GoogleAccountInfo $instance): void
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