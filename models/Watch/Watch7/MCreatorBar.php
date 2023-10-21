<?php
namespace Rehike\Model\Watch\Watch7;

use Rehike\Model\Common\MButton;
use Rehike\i18n\i18n;
use Rehike\Signin\API as SignIn;

class MCreatorBar {
    /** @var MButton[] */
    public $navButtons;

    /** @var MButton[] */
    public $editButtons;

    /**
     * @var string $vid   Video ID.
     */
    public function __construct($vid) {
        $i18n = i18n::getNamespace("watch");
        $ucid = SignIn::getInfo()["ucid"];

        $this->navButtons[] = new MButton([
            "text" => (object) [
                "simpleText" => $i18n->get("creatorBarAnalytics")
            ],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/video/$vid/analytics/tab-overview/period-default"
                    ]
                ]
            ],
            "customAttributes" => [
                "target" => "_blank"
            ]
        ]);
        $this->navButtons[] = new MButton([
            "text" => (object) [
                "simpleText" => $i18n->get("creatorBarVideoManager")
            ],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/channel/$ucid/videos/upload"
                    ]
                ]
            ],
            "customAttributes" => [
                "target" => "_blank"
            ]
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarInfo"),
            "icon" => "INFO",
            "url" => "//studio.youtube.com/video/$vid/edit"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarEnhancements"),
            "icon" => "ENHANCE",
            "url" => "//studio.youtube.com/video/$vid/editor"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarAudio"),
            "icon" => "AUDIO",
            "url" => "//studio.youtube.com/video/$vid/editor"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarComments"),
            "icon" => "ANNOTATIONS",
            "url" => "//studio.youtube.com/video/$vid/comments"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarCards"),
            "icon" => "CARDS",
            "url" => "//studio.youtube.com/video/$vid/edit"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->get("creatorBarSubtitles"),
            "icon" => "CAPTIONS",
            "url" => "//studio.youtube.com/video/$vid/translations"
        ]);
    }
}