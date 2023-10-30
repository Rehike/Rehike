<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\Util\ParsingUtils;

class MSubscriptionPreference
{
    public string $label;

    public bool $checked;

    public string $params;

    public string $class;

    public function __construct(array $data) 
    {
        $this->label = $data["label"] ?? "";
        $this->checked = $data["checked"] ?? false;
        $this->params = $data["params"] ?? false;
        $this->class = $data["class"] ?? false;
    }

    public static function fromData(object $data): self
    {
        $item = $data->menuServiceItemRenderer ?? null;
        return new self([
            "label" => ParsingUtils::getText($item->text) ?? "",
            "checked" => $item->isSelected ?? false,
            "params" => $item->serviceEndpoint->modifyChannelNotificationPreferenceEndpoint->params ?? "",
            "class" => match ($item->icon->iconType)
            {
                "NOTIFICATIONS_ACTIVE" => "receive-all-updates",
                "NOTIFICATIONS_NONE" => "receive-highlight-updates",
                "NOTIFICATIONS_OFF" => "receive-no-updates",
                default => ""
            }
        ]);
    }
}