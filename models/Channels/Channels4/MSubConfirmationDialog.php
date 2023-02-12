<?php
namespace Rehike\Model\Channels\Channels4;

use Rehike\i18n;
use Rehike\Model\Common\Subscription\MSubscriptionActions;
use Rehike\Model\Common\MButton;

class MSubConfirmationDialog
{
    public string $header;
    public string $displayName;
    public string $avatar;
    public MSubscriptionActions $subscribeButton;
    public MButton $closeButton;

    public function __construct(?MHeader $header)
    {
        $i18n = i18n::getNamespace("channels");
        $this->header = $i18n->subscriptionConfirmationHeader;

        $this->displayName = $header->title->text;
        $this->avatar = $header->thumbnail;
        $this->subscribeButton = $header->subscriptionButton;

        $gi18n = i18n::getNamespace("main/global");
        $this->closeButton = new MButton([
            "size" => "SIZE_DEFAULT",
            "style" => "STYLE_DEFAULT",
            "onclick" => "return!1",
            "attributes" => [
                "action" => "close"
            ],
            "class" => [
                "yt-dialog-dismiss",
                "yt-dialog-close"
            ],
            "text" => (object) [
                "simpleText" => $gi18n->close
            ]
        ]);
    }
}