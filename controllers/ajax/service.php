<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;

/**
 * Controller for the common service AJAX endpoint.
 * 
 * This includes things like liking videos.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        if (!@$request -> params -> name) self::error();

        $endpoint = $request -> params -> name;

        switch ($endpoint) {
            case "likeEndpoint":
                self::likeEndpoint();
                break;
            default:
                self::error();
                break;
        }
    }

    /**
     * Like endpoint.
     */
    private static function likeEndpoint() {
        $action = $_POST["action"];
        $videoId = $_POST["id"];

        Network::innertubeRequest(
            action: $action,
            body: [
                "target" => [
                    "videoId" => $videoId
                ]
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();

            if (!@$ytdata -> errors) {
                http_response_code(200);
                echo json_encode((object) [
                    "code" => "SUCCESS"
                ]);
                die();
            } else {
                self::error();
            }
        });
    }
};