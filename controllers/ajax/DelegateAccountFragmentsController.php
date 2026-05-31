<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\SignInV2\Info\YtChannelAccountInfo;
use Rehike\SignInV2\SignIn;

use Rehike\Controller\core\AjaxController;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

use Rehike\Model\Masthead\AccountPicker\MAccountPickerDelegateAccountItem;

/**
 * Delegate account fragments AJAX controller.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DelegateAccountFragmentsController extends AjaxController implements IGetController, IPostController
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
            $channelList[] = new MAccountPickerDelegateAccountItem($channel);
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
}