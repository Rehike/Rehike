<?php
namespace Rehike\Model\Masthead\Notifications;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

class MNotificationSettingsButton extends MButton
{
    public $targetId = "yt-masthead-notifications-settings";
    public $style = "STYLE_OPACITY";

    public function __construct()
    {
        $i18n = i18n::getNamespace("masthead");

        $this->accessibility = (object) [
            "accessibilityLabel" => (object) [
                "label" => $i18n->get("notificationsSettings")
            ]
        ];
        $this->icon = (object) [
            "iconType" => "ICON_ACCOUNT_SETTINGS"
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => "/account_notifications"
                ]
            ]
        ];
    }
}