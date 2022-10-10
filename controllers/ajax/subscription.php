<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $useTemplate = false;
    public $template = "ajax/subscription/get_subscription_preferences_overlay";

    public $ytdata;

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        switch ($action) {
            case "create_subscription_to_channel":
                $ytdata = self::createSubscriptionToChannel($yt, $request);
                break;
            default:
                http_response_code(400);
                echo json_encode((object) [
                    "errors" => []
                ]);
                die();
                break;
        }

        if (is_null($ytdata)) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => []
            ]);
            die();
        }

        if (!isset($ytdata -> error)) {
            http_response_code(200);
            echo json_encode((object) [
                "response" => "SUCCESS"
            ]);
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

    /**
     * Create a subscription to a channel.
     * TODO(aubymori): Make this actually work.
     *
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private function createSubscriptionToChannel(&$yt, $request) {
        $response = Request::innertubeRequest("subscription/subscribe", (object) [
            "channelIds" => [
                $_POST["c"] ?? null
            ],
            "params" => $_POST["params"] ?? null
        ]);
        return json_decode($response);
    }
};