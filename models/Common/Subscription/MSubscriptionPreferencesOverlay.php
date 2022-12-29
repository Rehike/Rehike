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

        $this->title = $i18n->notificationPrefsTitle($data["title"]);
        $this->saveButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "size" => "SIZE_DEFAULT",
            "text" => (object) [
                "simpleText" => $i18n->btnSave
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
                "simpleText" => $i18n->btnCancel
            ],
            "class" => ["yt-uix-overlay-close"]
        ]);

        $this->options = [];
        foreach ($data["options"] as $option) {
            $this->options[] = MSubscriptionPreference::fromData($option);
        }
    }
}