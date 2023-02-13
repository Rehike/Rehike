<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        if ($action == "add_to_watch_later_list") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];

            return self::addToPlaylist($videoId, "WL");
        } else if ($action == "delete_from_watch_later_list") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];

            return self::removeFromPlaylist($videoId, "WL");
        } else if ($action == "add_to_playlist") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];
            $listId = $_POST["full_list_id"];

            self::addToPlaylist($videoId, $listId);

            // Because YouTube's own server is a bit weird, this
            // might go too fast and break everything.
            // Hence: very gross fix for a server-side bug
            sleep(3);
        } else if ($action == "delete_from_playlist") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];
            $listId = $_POST["full_list_id"];

            self::removeFromPlaylist($videoId, $listId);

            sleep(3);
        } else {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    (object) [
                        "Specify an action!"
                    ]
                ]
            ]);
        }
    }

    protected static function validatePostVideoIds()
    {
        if(!isset($_POST["video_ids"])) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    (object) [
                        "Specify a video ID!"
                    ]
                ]
            ]);
        }
    }

    protected static function addToPlaylist($videoId, $plId)
    {
        $response = Request::innertubeRequest("browse/edit_playlist", (object) [
            "playlistId" => $plId,
            "actions" => [
                (object) [
                    "addedVideoId" => $videoId,
                    "action" => "ACTION_ADD_VIDEO"
                ]
            ]
        ]);
        $ytdata = json_decode($response);

        if ($ytdata->status = "STATUS_SUCCEEDED") {
            http_response_code(200);
            echo json_encode((object) []);
        } else {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    (object) [
                        "Failed to add video to playlist"
                    ]
                ]
            ]);
        }
    }

    protected static function removeFromPlaylist($videoId, $plId)
    {
        $response = Request::innertubeRequest("browse/edit_playlist", (object) [
            "playlistId" => $plId,
            "actions" => [
                (object) [
                    "removedVideoId" => $videoId,
                    "action" => "ACTION_REMOVE_VIDEO_BY_VIDEO_ID"
                ]
            ]
        ]);
        $ytdata = json_decode($response);

        if ($ytdata->status = "STATUS_SUCCEEDED") {
            http_response_code(200);
            echo json_encode((object) []);
        } else {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    (object) [
                        "Failed to remove video from playlist"
                    ]
                ]
            ]);
        }
    }
};