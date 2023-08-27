<?php
namespace Rehike\SignInV2\Builder;

use Rehike\SignInV2\Info\GoogleAccountInfo;

/**
 * Builder for the GoogleAccountInfo class.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class GoogleAccountInfoBuilder
{
    private SessionInfoBuilder $parent;
    
    private array $ytChannels = [];

    public function __construct(SessionInfoBuilder $parent)
    {
        $this->parent = $parent;
    }

    public function build(): GoogleAccountInfo
    {

    }

    public function insertYtChannel(): YtChannelAccountInfoBuilder
    {
        $instance = new YtChannelAccountInfoBuilder($this);
        $this->ytChannels[] = $instance;
        return $instance;
    }
}