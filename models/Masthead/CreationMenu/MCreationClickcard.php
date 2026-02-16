<?php
namespace Rehike\Model\Masthead\CreationMenu;

use Rehike\i18n\i18n;
use Rehike\SignInV2\SignIn;

class MCreationClickcard
{
    public string $template = "masthead_creation_menu";

    /**
     * @inheritDoc
     */
    public array $cardClass = [
        "yt-scrollbar",
        "yt-masthead-creation-clickcard"
    ];

    public string $id = "yt-masthead-creation-menu";
    public string $cardId = "yt-masthead-creation-clickcard";
    public object $content;

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $signInInfo = SignIn::getSessionInfo();
        $hasChannel = SignIn::isSignedIn() && !is_null($signInInfo->getUcid());

        if ($hasChannel)
            $ucid = $signInInfo->getUcid();

        $items = [];

        $items[] = new MCreationMenuItem(
            "upload",
            $i18n->get("creationUpload"),
            $hasChannel ? "//studio.youtube.com/channel/$ucid/videos?d=ud" : "/create_channel?upsell=upload&next=/"
        );
        $items[] = new MCreationMenuItem(
            "live",
            $i18n->get("creationLive"),
            $hasChannel ? "//studio.youtube.com/channel/$ucid/livestreaming" : "/create_channel?upsell=livestreaming&next=/"
        );
        if ($hasChannel) $items[] = new MCreationMenuItem(
            "post",
            $i18n->get("creationPost"),
            $hasChannel ? "/channel/$ucid/community?show_create_dialog=1" : "/create_chanel?upsell=community&next=/"
        );

        $this->content = (object) [
            "items" => $items
        ];
    }
}