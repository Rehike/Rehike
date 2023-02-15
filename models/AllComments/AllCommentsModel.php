<?php
namespace Rehike\Model\AllComments;

use \Rehike\Model\Watch\WatchModel;
use \Rehike\Model\AllComments\MLockupFromWatchModel;

class AllCommentsModel {
    public static function bake(&$yt, $data) {
        $response = (object) [];
        $watchData = WatchModel::bake($yt, $data, $yt -> videoId);
        $response -> videoDiscussionRenderer = $watchData -> results -> videoDiscussionRenderer;

        return $response;
    }
}