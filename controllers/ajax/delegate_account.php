<?php
namespace Rehike\Controller\ajax;

use \Rehike\Signin\API as SignIn;

return new class extends \Rehike\Controller\core\AjaxController {
    public $template = "ajax/delegate_account";

    protected $spfIdListeners = [
        "yt-delegate-accounts"
    ];

    public function onGet(&$yt, $request) {
        return $this->onPost($yt, $request);
    }

    public function onPost(&$yt, $request) {
        if (!SignIn::isSignedIn()) {
            self::error();
        }

        $info = SignIn::getInfo();

        $channelList = [];
        foreach ($info["channelPicker"] as $channel) {
            $channelList[] = (object) $channel;
        }

        for ($i = 0; $i < count($channelList); $i++) {
            if ($channelList[$i]->selected) {
                array_splice($channelList, $i, 1);
                $i--;
            }
        }

        $yt->page = $channelList;
    }
};