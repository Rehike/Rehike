<?php
namespace Rehike\Model\Masthead\CreationMenu;

use Rehike\i18n;
use Rehike\Signin\API as SignIn;

class MCreationClickcard {
    public $template = "masthead_creation_menu";
    public $cardClass = [
        "yt-scrollbar",
        "yt-masthead-creation-clickcard"
    ];
    public $id = "yt-masthead-creation-menu";
    public $cardId = "yt-masthead-creation-clickcard";
    public $content;

    public function __construct() {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = (object) SignIn::getInfo();
        $hasChannel = SignIn::isSignedIn() && isset($signInInfo->ucid);
        if ($hasChannel) $ucid = $signInInfo->ucid;

        $items = [];

        $items[] = new MCreationMenuItem(
            "upload",
            $i18n->creationUpload,
            $hasChannel ? "//studio.youtube.com/channel/$ucid/videos?d=ud" : "/create_channel?upsell=upload&next=/"
        );
        $items[] = new MCreationMenuItem(
            "live",
            $i18n->creationLive,
            $hasChannel ? "//studio.youtube.com/channel/$ucid/livestreaming" : "/create_channel?upsell=livestreaming&next=/"
        );
        if ($hasChannel) $items[] = new MCreationMenuItem(
            "post",
            $i18n->creationPost,
            $hasChannel ? "/channel/$ucid/community?show_create_dialog=1" : "/create_chanel?upsell=community&next=/"
        );

        $this->content = (object) [
            "items" => $items
        ];
    }
}