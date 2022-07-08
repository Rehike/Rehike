<?php
use Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $template = "ajax/feed/get_notifications";

    public function onGet(&$yt, $request) {
        header("Content-Type: application/json");

        if (!@$yt->signin["isSignedIn"]) { // feed_ajax is ONLY used signed in
            echo json_encode((object)["errors"=>[]]);
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