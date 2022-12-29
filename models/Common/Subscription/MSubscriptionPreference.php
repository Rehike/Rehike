<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\TemplateFunctions;

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
        $this->label = $data["label"] ?? "";
        $this->checked = $data["checked"] ?? false;
        $this->params = $data["params"] ?? false;
        $this->class = $data["class"] ?? false;
    }

    public static function fromData($data) {
        $item = $data->menuServiceItemRenderer ?? null;
        return new self([
            "label" => TemplateFunctions::getText($item->text) ?? "",
            "checked" => $item->isSelected ?? false,
            "params" => $item->serviceEndpoint->modifyChannelNotificationPreferenceEndpoint->params ?? "",
            "class" => (function() use ($item) {
                switch ($item->icon->iconType) {
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