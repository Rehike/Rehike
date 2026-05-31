<?php
namespace Rehike\Model\Masthead\AccountPicker;

use Rehike\SignInV2\Info\YtChannelAccountInfo;

/**
 * Model for delegate accounts
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MAccountPickerDelegateAccountItem
{
    public string $name;
    public string $byline;
    public string $photo;
    public string $switchUrl;
    
    public YtChannelAccountInfo $infoSource;
    
    public function __construct(YtChannelAccountInfo $channelInfo)
    {
        $this->name = $channelInfo->getDisplayName() ?? "";
        $this->byline = $channelInfo->getOwnerAccount()->isActive()
            ? $channelInfo->getLocalizedSubscriberCount() ?? ""
            : $channelInfo->getAccountEmail() ?? "";
        $this->photo = $channelInfo->getAvatarUrl() ?? "";
        $this->switchUrl = $channelInfo->getSwitchUrl("/?feature=masthead_switcher") ?? "";
        
        $this->infoSource = $channelInfo;
    }
}