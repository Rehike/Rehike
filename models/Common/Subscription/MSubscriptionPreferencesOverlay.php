<?php
namespace Rehike\Model\Common\Subscription;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;

class MSubscriptionPreferencesOverlay {
    public string $title;

    /** @var MSubscriptionPreference[] */
    public array $options = [];

    public MButton $saveButton;

    public MButton $cancelButton;

    public function __construct($data) {
        $i18n = i18n::getNamespace("misc");

        $this->title = $i18n->format("notificationPrefsTitle", $data["title"]);
        $this->saveButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n->get("btnSave")
            ],
            "class" => [
                "overlay-confirmation-preferences-update-frequency",
                "yt-uix-overlay-close"
            ]
        ]);
        $this->cancelButton = new MButton([
            "style" => "STYLE_DEFAULT",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n->get("btnCancel")
            ],
            "class" => ["yt-uix-overlay-close"]
        ]);

        foreach ($data["options"] as $option)
        if ($option->menuServiceItemRenderer->icon->iconType != "PERSON_MINUS")
        // ^ Filter out "Unsubscribe" item
        {
            $this->options[] = MSubscriptionPreference::fromData($option);
        }
    }
}