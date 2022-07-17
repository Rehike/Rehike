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

    public function __construct($sidebar, $header) {
        $priInfo = $sidebar -> items[0] -> playlistSidebarPrimaryInfoRenderer;
        $secInfo = $sidebar -> items[1] -> playlistSidebarSecondaryInfoRenderer;

        $this -> thumbnail = $priInfo -> thumbnailRenderer -> playlistVideoThumbnailRenderer -> thumbnail;
        $this -> title = $priInfo -> title;
        $this -> navigationEndpoint = $priInfo -> navigationEndpoint;
        $this -> owner = $secInfo -> videoOwner -> videoOwnerRenderer;
        $this -> metas = $priInfo -> stats;
        $this -> actions = [];
        $this -> actions[] = new MButton((object) [
            "anchor" => true,
            "spf" => true,
            "style" => "default",
            "size" => "default",
            "content" => (object) [
                "runs" => [
                    (object) [
                        "text" => "Play all"
                    ]
                ]
            ],
            "hasIcon" => true,
            "noIconMarkup" => true,
            "class" => [
                "playlist-play-all",
                "yt-uix-button-playlist-action",
                "play-all-icon-btn"
            ],
            "href" => TemplateFunctions::getUrl($priInfo)
        ]);

        if (isset($header -> shareButton)) $this -> actions[] = new MButton((object) [
            "style" => "default",
            "size" => "default"
        ]);
    }
}