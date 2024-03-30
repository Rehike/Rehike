<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Rehike\Signin\API as SignIn;

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

        $info = SignIn::getInfo();

        $channelList = [];
        foreach ($info["channelPicker"] as $channel) 
        {
            $channelList[] = (object) $channel;
        }

        for ($i = 0; $i < count($channelList); $i++) 
        {
            if ($channelList[$i]->selected) 
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