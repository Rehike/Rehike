<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;

/**
 * Controller for playlist AJAX endpoints.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        if ($action == "add_to_watch_later_list") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];

            self::addToPlaylist($videoId, "WL");
        } else if ($action == "delete_from_watch_later_list") {
            self::validatePostVideoIds();

            $videoId = $_POST["video_ids"];

            self::removeFromPlaylist($videoId, "WL");
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
        } else if (isset($action)) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    (object) [
                        "Illegal action $action."
                    ]
                ]
            ]);
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

    /**
     * Check if the request includes the POST form parameter for video_ids.
     * 
     * If it isn't set, then it's an illegal request and this will reject the
     * request.
     */
    protected static function validatePostVideoIds(): void
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

    /**
     * Add a video to a playlist.
     */
    protected static function addToPlaylist(
            string $videoId, 
            string $plId
    ): void
    {
        Network::innertubeRequest(
            action: "browse/edit_playlist",
            body: [
                "playlistId" => $plId,
                "actions" => [
                    (object) [
                        "addedVideoId" => $videoId,
                        "action" => "ACTION_ADD_VIDEO"
                    ]
                ]
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();

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
        });
    }

    /**
     * Remove a video from a playlist.
     */
    protected static function removeFromPlaylist(
            string $videoId, 
            string $plId
    ): void
    {
        Network::innertubeRequest(
            action: "browse/edit_playlist",
            body: [
                "playlistId" => $plId,
                "actions" => [
                    (object) [
                        "removedVideoId" => $videoId,
                        "action" => "ACTION_REMOVE_VIDEO_BY_VIDEO_ID"
                    ]
                ]
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();

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
        });
    }
};