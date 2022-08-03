<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        if (!@$request -> params -> name) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    "You must specify an endpoint name! (\$_GET[\"name\"])"
                ]
            ]);
            die();
        }

        $endpoint = $request -> params -> name;

        if ($endpoint == "likeEndpoint") {
            $errors = (object) [
                "errors" => []
            ];
            if (!@$_POST["action"]) {
                $errors -> errors[] = "Specify an action!";
            }
            if (!@$_POST["id"]) {
                $errors -> errors[] = "Specify a video ID!";
            }
            if (@$errors -> errors[0]) {
                http_response_code(400);
                echo json_encode($errors);
                die();
            }

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
                    "code" => "SUCCESS",
                    "data" => $ytdata
                ]);
                die();
            } else {
                $errors = (object) [
                    "errors" => []
                ];

                for ($i = 0; $i < count($ytdata -> error -> errors); $i++) {
                    $errors[] = $ytdata -> error -> errors[$i] -> message ?? null;
                }

                http_response_code(400);
                echo json_encode($errors);
            }
        }
    }
};