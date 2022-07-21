<?php
namespace Rehike\Model\AllComments;

use \Rehike\Model\WatchModel;
use \Rehike\Model\AllComments\MLockupFromWatchModel;

class AllCommentsModel {
    public static function bake(&$yt, $data) {
        $response = (object) [];
        $watchData = WatchModel::bake($yt, $data);
        $response -> videoDiscussionRenderer = $watchData -> results -> videoDiscussionRenderer;

        return $response;
    }
}