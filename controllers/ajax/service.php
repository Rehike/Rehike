<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

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

        $response = Request::innertubeRequest("like/" . $action, (object) [
            "target" => (object) [
                "videoId" => $videoId
            ]
        ]);
        $ytdata = json_decode($response);

        if (!@$ytdata -> errors) {
            http_response_code(200);
            echo json_encode((object) [
                "code" => "SUCCESS"
            ]);
            die();
        } else {
            self::error();
        }
    }
};