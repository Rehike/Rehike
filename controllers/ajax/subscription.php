<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\Async\Promise;
use \Rehike\Model\Common\Subscription\MSubscriptionPreferencesOverlay;

/**
 * Controller for subscription actions.
 * 
 * This includes subscribing, unsubscribing, and getting subscription
 * preferences.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    // These are used by the preferences overlay response.
    public $useTemplate = false;
    public $template = "";

    public function onPost(&$yt, $request) {
        $action = self::findAction();

        switch ($action) {
            case "create_subscription_to_channel":
                $request = self::createSubscriptionToChannel();
                break;
            case "remove_subscriptions":
                $request = self::removeSubscriptions();
                break;
            case "get_subscription_preferences_overlay":
                $this->useTemplate = true;
                $this->template = 
                    "ajax/subscription/get_subscription_preference_overlay"
                ;
                self::getPreferencesOverlay($yt, $request);
                return; // This takes control of everything from here.
            default:
                self::error();
                break;
        }

        $request->then(function ($ytdata) {
            if (is_null($ytdata)) self::error();

            if (!isset($ytdata->error)) {
                http_response_code(200);
                echo json_encode((object) [
                    "response" => "SUCCESS"
                ]);
            } else self::error();
        });
    }

    /**
     * Create a subscription to a channel.
     *
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private static function createSubscriptionToChannel(): Promise {
        return new Promise(function ($resolve) {
            Network::innertubeRequest(
                action: "subscription/subscribe",
                body: [
                    "channelIds" => [
                        $_GET["c"] ?? null
                    ],
                    "params" => $_POST["params"] ?? null
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }

    /**
     * Remove a subscription from a channel.
     * 
     * @param object          $yt      Template data.
     * @param RequestMetadata $request Request data.
     */
    private static function removeSubscriptions(): Promise {
        return new Promise(function ($resolve) {
            Network::innertubeRequest(
                action: "subscription/unsubscribe",
                body: [
                    "channelIds" => [
                        $_GET["c"] ?? null
                    ]
                ]
            )->then(function ($response) use ($resolve) {
                $resolve( $response->getJson() );
            });
        });
    }

    /**
     * Get the subscription preferences overlay.
     * 
     * @param object           $yt       Template data.
     * @param RequestMetadata  $request  Request data.
     */
    private static function getPreferencesOverlay(&$yt, 
                                                  $request): void {
        Network::innertubeRequest(
            action: "browse",
            body: [
                "browseId" => $_POST["c"] ?? ""
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();
            $header = $ytdata->header->c4TabbedHeaderRenderer ?? null;
            $yt->page = new MSubscriptionPreferencesOverlay([
                "title" => $header->title ?? "",
                "options" => ($header 
                    ->subscribeButton 
                    ->subscribeButtonRenderer 
                    ->notificationPreferenceButton 
                    ->subscriptionNotificationToggleButtonRenderer 
                    ->command 
                    ->commandExecutorCommand 
                    ->commands[0] 
                    ->openPopupAction 
                    ->popup 
                    ->menuPopupRenderer 
                    ->items) ?? []
            ]);
        });
    }
};