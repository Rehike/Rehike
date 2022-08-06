<?php
use Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $template = "ajax/feed/get_notifications";

    public function onGet(&$yt, $request) {
        if (!@$yt->signin["isSignedIn"]) { // feed_ajax is ONLY used signed in
            echo json_encode((object)["errors"=>["You must be signed in"]]);
            die();
        }

        $action = self::findAction();

        if (@$action == "get_unseen_notification_count") {
            $this -> template = "ajax/feed/get_unseen_notification_count";

            $response = Request::innertubeRequest("notification/get_unseen_count");
            $ytdata = json_decode($response);

            $updateAction = $ytdata->actions[0]->updateNotificationsUnseenCountAction;

            $yt->unseenCount = $updateAction->unseenCount ?? $ytdata->unseenCount ?? null;
            $yt->pollingTimeout = $updateAction->timeoutMs ?? 1800000;
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

            $yt->notifList = $ytdata->actions[0]->openPopupAction->popup->multiPageMenuRenderer->sections[0]->multiPageMenuNotificationSectionRenderer->items;
        }
    }

    public function onPost(&$yt, $request) {
        $this -> spfIdListeners = [
            "yt-masthead-notifications-content"
        ];

        $response = Request::innertubeRequest("notification/get_notification_menu", (object) [
            "notificationsMenuRequestType" => "NOTIFICATIONS_MENU_REQUEST_TYPE_INBOX"
        ]);
        $ytdata = json_decode($response);

        $yt->notifList = $ytdata->actions[0]->openPopupAction->popup->multiPageMenuRenderer->sections[0]->multiPageMenuNotificationSectionRenderer->items;
    }
};