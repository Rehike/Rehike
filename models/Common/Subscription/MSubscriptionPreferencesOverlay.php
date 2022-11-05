<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\Model\Common\MButton;
use Rehike\i18n;
use Rehike\TemplateFunctions;

class MSubscriptionPreferencesOverlay {
    /** @var string */
    public $title;

    /** @var object[] */
    public $options;

    /** @var MButton */
    public $saveButton;

    /** @var MButton */
    public $cancelButton;

    public function __construct($data) {
        $i18n = i18n::getNamespace("main/misc");

        $this -> title = $i18n -> notificationPrefsTitle($data["title"]);
        $this -> saveButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n -> btnSave
            ],
            "class" => [
                "overlay-confirmation-preferences-update-frequency",
                "yt-uix-overlay-close"
            ]
        ]);
        $this -> cancelButton = new MButton([
            "style" => "STYLE_DEFAULT",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n -> btnCancel
            ],
            "class" => ["yt-uix-overlay-close"]
        ]);

        $this -> options = [];
        foreach ($data["options"] as $option) {
            $this -> options[] = MSubscriptionPreference::fromData($option);
        }
    }
}

class MSubscriptionPreference {
    /** @var string */
    public $label;

    /** @var bool; */
    public $checked;

    /** @var string */
    public $params;

    /** @var string */
    public $class;

    public function __construct($data) {
        $this -> label = $data["label"] ?? "";
        $this -> checked = $data["checked"] ?? false;
        $this -> params = $data["params"] ?? false;
        $this -> class = $data["class"] ?? false;
    }

    public static function fromData($data) {
        $item = $data -> menuServiceItemRenderer ?? null;
        return new self([
            "label" => TemplateFunctions::getText($item -> text) ?? "",
            "checked" => $item -> isSelected ?? false,
            "params" => $item -> serviceEndpoint -> modifyChannelNotificationPreferenceEndpoint -> params ?? "",
            "class" => (function() use ($item) {
                switch ($item -> icon -> iconType) {
                    case "NOTIFICATIONS_ACTIVE":
                        return "receive-all-updates";
                    case "NOTIFICATIONS_NONE":
                        return "receive-highlight-updates";
                    case "NOTIFICATIONS_OFF":
                        return "receive-no-updates";
                    default:
                        return "";
                }
            })()
        ]);
    }
}