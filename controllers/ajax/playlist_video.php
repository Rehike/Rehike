<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        if ($action == "add_to_watch_later_list") {
            // Invalid request
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

            $yt -> videoId = $_POST["video_ids"];

            $response = Request::innertubeRequest("browse/edit_playlist", (object) [
                "playlistId" => "WL",
                "actions" => [
                    (object) [
                        "addedVideoId" => $yt -> videoId,
                        "action" => "ACTION_ADD_VIDEO"
                    ]
                ]
            ]);
            $ytdata = json_decode($response);

            if ($ytdata -> status = "STATUS_SUCCEEDED") {
                http_response_code(200);
                echo json_encode((object) []);
            } else {
                http_response_code(400);
                echo json_encode((object) [
                    "errors" => [
                        (object) [
                            "Failed to add video to Watch Later"
                        ]
                    ]
                ]);
            }
        } else if ($action == "delete_from_watch_later_list") {
            // Invalid request
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

            $yt -> videoId = $_POST["video_ids"];

            $response = Request::innertubeRequest("browse/edit_playlist", (object) [
                "playlistId" => "WL",
                "actions" => [
                    (object) [
                        "removedVideoId" => $yt -> videoId,
                        "action" => "ACTION_REMOVE_VIDEO_BY_VIDEO_ID"
                    ]
                ]
            ]);
            $ytdata = json_decode($response);

            if ($ytdata -> status = "STATUS_SUCCEEDED") {
                http_response_code(200);
                echo json_encode((object) []);
            } else {
                http_response_code(400);
                echo json_encode((object) [
                    "errors" => [
                        (object) [
                            "Failed to remove video from Watch Later"
                        ]
                    ]
                ]);
            }
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
};