<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\SignInV2\SignIn;

/**
 * Model for delegate accounts
 * 
 * This is a lazy hack to maintain the model structure that the template expects
 * from sign in V1.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MDelegateAccountItem
{
    public string $name;
    public string $byline;
    public string $photo;
    public string $switchUrl;
    
    public YtChannelAccountInfo $infoSource;
    
    public function __construct(YtChannelAccountInfo $channelInfo)
    {
        $this->name = $channelInfo->getDisplayName() ?? "";
        $this->byline = $channelInfo->getLocalizedSubscriberCount() ?? "";
        $this->photo = $channelInfo->getAvatarUrl() ?? "";
        $this->switchUrl = $channelInfo->getSwitchUrl("/?feature=masthead_switcher") ?? "";
        
        $this->infoSource = $channelInfo;
    }
}

return new class extends \Rehike\Controller\core\AjaxController 
{
    public string $template = "ajax/delegate_account";

    public function onGet(YtApp $yt, RequestMetadata $request): void 
    {
        $this->onPost($yt, $request);
    }

    public function onPost(YtApp $yt, RequestMetadata $request): void 
    {
        if (!SignIn::isSignedIn()) 
        {
            self::error();
        }

        $sessionInfo = SignIn::getSessionInfo();

        $channelList = [];
        foreach ($sessionInfo->getCurrentGoogleAccount()->getYoutubeChannels() as $channel) 
        {
            $channelList[] = new MDelegateAccountItem($channel);
        }

        for ($i = 0; $i < count($channelList); $i++) 
        {
            if ($channelList[$i]->infoSource->isActive()) 
            {
                array_splice($channelList, $i, 1);
                $i--;
            }
        }

        $yt->page = (object)[
            "channels" => $channelList
        ];
    }
};