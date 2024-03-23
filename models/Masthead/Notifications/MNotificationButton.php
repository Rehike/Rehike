<?php
namespace Rehike\Model\Masthead\Notifications;

use Rehike\Model\Common\MButton;

class MNotificationButton extends MButton
{
    public $targetId = "yt-masthead-notifications-button";
    public $class = [
        "sb-notif-off"
    ];
    public $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct()
    {
        $this->accessibility = (object) [
            "accessibilityData" => (object) [
                "haspopup" => "true"
            ]
        ];
        $this->icon = (object) [
            "iconType" => "BELL"
        ];
        $this->text = (object) [
            "simpleText" => "0"
        ];
        $this->clickcard = new MNotificationClickcard();
    }
}