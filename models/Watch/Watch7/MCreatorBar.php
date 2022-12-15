<?php
namespace Rehike\Model\Watch\Watch7;

use Rehike\Model\Common\MButton;
use Rehike\i18n;
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
        $i18n = i18n::newNamespace("watch/creator_bar");
        $i18n->registerFromFolder("i18n/watch");
        $ucid = SignIn::getInfo()["ucid"];

        $this->navButtons[] = new MButton([
            "text" => (object) [
                "simpleText" => $i18n->creatorBarAnalytics
            ],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/video/$vid/analytics/tab-overview/period-default"
                    ]
                ]
            ]
        ]);
        $this->navButtons[] = new MButton([
            "text" => (object) [
                "simpleText" => $i18n->creatorBarVideoManager
            ],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => "//studio.youtube.com/channel/$ucid/videos/upload"
                    ]
                ]
            ]
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarInfo,
            "icon" => "INFO",
            "url" => "//studio.youtube.com/video/$vid/edit"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarEnhancements,
            "icon" => "ENHANCE",
            "url" => "//studio.youtube.com/video/$vid/editor"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarAudio,
            "icon" => "AUDIO",
            "url" => "//studio.youtube.com/video/$vid/editor"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarComments,
            "icon" => "ANNOTATIONS",
            "url" => "//studio.youtube.com/video/$vid/comments"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarCards,
            "icon" => "CARDS",
            "url" => "//studio.youtube.com/video/$vid/edit"
        ]);

        $this->editButtons[] = new MCreatorBarEditButton([
            "tooltip" => $i18n->creatorBarSubtitles,
            "icon" => "CAPTIONS",
            "url" => "//studio.youtube.com/video/$vid/translations"
        ]);
    }
}

class MCreatorBarEditButton extends MButton {
    public $style = "STYLE_TEXT_DARK";

    public function __construct($data) {
        $this->itemTooltip = $data["tooltip"];
        $this->icon = (object) [
            "iconType" => $data["icon"]
        ];
        $this->navigationEndpoint = (object) [
            "commandMetadata" => (object) [
                "webCommandMetadata" => (object) [
                    "url" => $data["url"]
                ]
            ]
        ];
    }
}