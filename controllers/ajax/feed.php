<?php
use Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $template = "ajax/feed/get_notifications";

    public function onGet(&$yt, $request) {
        if (!@$yt->signin["isSignedIn"]) self::error();

        $action = self::findAction();

        if (@$action == "get_unseen_notification_count") {
            $this -> useTemplate = false;

            $response = Request::innertubeRequest("notification/get_unseen_count");
            $ytdata = json_decode($response);

            $updateAction = $ytdata->actions[0]->updateNotificationsUnseenCountAction;

            echo json_encode((object) [
                "unseen_notification_count" => $updateAction->unseenCount ?? $ytdata->unseenCount ?? null,
                "timestamp_lower_bound" => 0,
                "high_priority_notification_timeout_ms" => 3000,
                "polling_timeout" => $updateAction->timeoutMs ?? 1800000
            ]);
        } elseif (@$action == "continuation") {
            $this -> template = "ajax/feed/continuation";
            
            if (!@$request -> params -> continuation) {
                echo json_encode((object) [
                    "errors" => [
                        "Specify a continuation"
                    ]
                ]);
                die();
            }

            $response = Request::innertubeRequest("notification/get_notification_menu", (object) [
                "ctoken" => $request -> params -> continuation ?? null
            ]);
            $ytdata = json_decode($response);

            $yt -> notifList = $ytdata -> actions[0] -> appendContinuationItemsAction -> continuationItems ?? null;
            $yt -> nextContinuation = end($yt -> notifList) -> continuationItemRenderer -> continuationEndpoint -> getNotificationMenuEndpoint -> ctoken ?? null;
        } else {
            $this -> spfIdListteners = [
                "yt-masthead-notifications-content"
            ];

            $response = Request::innertubeRequest("notification/get_notification_menu", (object) [
                "notificationsMenuRequestType" => "NOTIFICATIONS_MENU_REQUEST_TYPE_INBOX"
            ]);
            $ytdata = json_decode($response);

            $yt->notifSections = $ytdata->actions[0]->openPopupAction->popup->multiPageMenuRenderer->sections;
        }
    }

    public function onPost(&$yt, $request) {
        if (!@$yt->signin["isSignedIn"]) self::error();

        $this -> spfIdListeners = [
            "yt-masthead-notifications-content"
        ];

        $response = Request::innertubeRequest("notification/get_notification_menu", (object) [
            "notificationsMenuRequestType" => "NOTIFICATIONS_MENU_REQUEST_TYPE_INBOX"
        ]);
        $ytdata = json_decode($response);

        $yt->notifSections = $ytdata->actions[0]->openPopupAction->popup->multiPageMenuRenderer->sections;
    }
};