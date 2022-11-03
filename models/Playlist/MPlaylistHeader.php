<?php
namespace Rehike\Model\Playlist;

use \Rehike\Model\Common\MButton;
use \Rehike\TemplateFunctions;

class MPlaylistHeader {
    public $thumbnail;
    public $title;
    public $navigationEndpoint;
    public $owner;
    public $metas;
    public $actions;

    public function __construct($sidebar) {
        $priInfo = $sidebar -> items[0] -> playlistSidebarPrimaryInfoRenderer ?? null;
        $secInfo = $sidebar -> items[1] -> playlistSidebarSecondaryInfoRenderer ?? null;

        $this -> thumbnail = $priInfo -> thumbnailRenderer -> playlistVideoThumbnailRenderer -> thumbnail;
        $this -> title = (function() use ($priInfo) {
            if (isset($priInfo -> title)) {
                return TemplateFunctions::getText($priInfo -> title);
            } else if (isset($priInfo -> titleForm -> inlineFormRenderer -> formField -> textInputFormFieldRenderer -> value)) {
                return $priInfo -> titleForm -> inlineFormRenderer -> formField -> textInputFormFieldRenderer -> value;
            } else {
                return "";
            }
        })();//TemplateFunctions::getText($priInfo -> title) ?? $priInfo -> titleForm -> inlineFormRenderer -> formField -> textInputFormFieldRenderer -> value ?? null;
        $this -> navigationEndpoint = $priInfo -> navigationEndpoint;
        $this -> owner = $secInfo -> videoOwner -> videoOwnerRenderer;
        $this -> metas = $priInfo -> stats;
        $this -> actions = [];
        $this -> actions[] = new MButton((object) [
            "style" => "default",
            "size" => "default",
            "text" => (object) [
                "runs" => [
                    (object) [
                        "text" => "Play all"
                    ]
                ]
            ],
            "icon" => (object) [],
            "class" => [
                "playlist-play-all",
                "yt-uix-button-playlist-action",
                "play-all-icon-btn"
            ],
            "navigationEndpoint" => (object) [
                "commandMetadata" => (object) [
                    "webCommandMetadata" => (object) [
                        "url" => TemplateFunctions::getUrl($priInfo)
                    ]
                ]
            ]
        ]);
    }
}