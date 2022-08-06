<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $useTemplate = false;
    public $template = "ajax/subscription/get_subscription_preferences_overlay";

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        if ($action == "create_subscription_to_channel") {
            $response = Request::innertubeRequest("subscription/subscribe", (object) [
                "channelIds" => [
                    $_POST["c"] ?? null
                ]/*,
                "params" => "EgIIDRgA"*/
            ]);
            $ytdata = json_decode($response);

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
        } else if ($action == "remove_subscriptions") {

        }
    }
};