<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\Model\Common\MButton;
use Rehike\i18n;

class MSubscriptionPreferencesButton extends MButton {
    public $class = [
        "yt-uix-subscription-preferences-button"
    ];

    public function __construct($ucid, $stateId) {
        $i18n = i18n::getNamespace("main/misc");

        $this -> attributes["channel-external-id"] = $ucid;
        $this -> accessibility = (object) [
            "accessibilityData" => (object) [
                "live" => "polite",
                "busy" => "false",
                "role" => "button",
                "label" => $i18n -> notificationPrefsLabel
            ]
        ];
        $this -> icon = (object) [
            "iconType" => "SUBSCRIPTION_PREFERENCES"
        ];

        switch ($stateId) {
            case 0:
                $this -> class[] = "yt-uix-subscription-notifications-none";
                break;
            case 2:
                $this -> class[] = "yt-uix-subscription-notifications-all";
                break; 
        }
    }
}