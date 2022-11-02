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
                $ytdata = self::createSubscriptionToChannel();
                break;
            case "remove_subscriptions":
                $ytdata = self::removeSubscriptions();
                break;
            default:
                self::error();
                break;
        }

        if (is_null($ytdata)) self::error();

        if (!isset($ytdata -> error)) {
            http_response_code(200);
            echo json_encode((object) [
                "response" => "SUCCESS"
            ]);
        } else self::error();
    }

    /**
     * Create a subscription to a channel.
     *
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private function createSubscriptionToChannel() {
        $response = Request::innertubeRequest("subscription/subscribe", (object) [
            "channelIds" => [
                $_GET["c"] ?? null
            ],
            "params" => $_POST["params"] ?? null
        ]);
        return json_decode($response);
    }

    /**
     * Remove a subscription from a channel.
     * 
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private function removeSubscriptions() {
        $response = Request::innertubeRequest("subscription/unsubscribe", (object) [
            "channelIds" => [
                $_GET["c"] ?? null
            ]
        ]);
        return json_decode($response);
    }
};