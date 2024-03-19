<?php
namespace Rehike\Model\Playlist;

use Rehike\Model\Common\MAbstractClickcard;

class MPlaylistShareClickcard extends MAbstractClickcard {
    public $template = "playlist_share";
    public $cardClass = [
        "yt-card"
    ];
    public $class = "pl-header-sharepanel-content";
    public $targetWrapper;

    public function __construct() {
        $this->targetWrapper = (object) [
            "position" => "bottomright",
            "orientation" => "vertical"
        ];
    }
}