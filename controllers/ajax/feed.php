<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use SpfPhp\SpfPhp;

/**
 * Controller for AJAX feeds.
 * 
 * This is only used for the notifications page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController
{
    public string $template = "ajax/feed/get_notifications";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        if (!@$yt->signin["isSignedIn"]) self::error();

        $action = self::findAction();

        if (@$action == "get_unseen_notification_count")
        {
            $this->useTemplate = false;

            Network::innertubeRequest(
                action: "notification/get_unseen_count"
            )->then(function ($response) {
                $ytdata = $response->getJson();

                $updateAction = $ytdata->actions[0]->updateNotificationsUnseenCountAction;

                echo json_encode((object) [
                    "unseen_notification_count" => $updateAction->unseenCount ?? $ytdata->unseenCount ?? null,
                    "timestamp_lower_bound" => 0,
                    "high_priority_notification_timeout_ms" => 3000,
                    "polling_timeout" => $updateAction->timeoutMs ?? 1800000
                ]);
            });
        }
        else if (@$action == "continuation")
        {
            $this->template = "ajax/feed/continuation";
            
            if (!@$request->params->continuation)
            {
                echo json_encode((object) [
                    "errors" => [
                        "Specify a continuation"
                    ]
                ]);
                die();
            }

            Network::innertubeRequest(
                action: "notification/get_notification_menu",
                body: [
                    "ctoken" => $request->params->continuation ?? null
                ]
            )->then(function ($response) use ($yt) {
                $ytdata = $response->getJson();

                $yt->page->notifList = $ytdata->actions[0] ->appendContinuationItemsAction->continuationItems ?? null;
                $yt->page->nextContinuation = (end($yt->notifList) 
                    ->continuationItemRenderer 
                    ->continuationEndpoint 
                    ->getNotificationMenuEndpoint 
                    ->ctoken) ?? null;
            });
        }
        else
        {
            $this->getNotifications($yt, $request);
        }
    }

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        if (!@$yt->signin["isSignedIn"]) self::error();

        $this->getNotifications($yt, $request);
    }

    private function getNotifications(YtApp $yt, RequestMetadata $request): void
    {
        $this->spfIdListeners = [
            "yt-masthead-notifications-content"
        ]; 

        Network::innertubeRequest(
            action: "notification/get_notification_menu",
            body: [
                "notificationsMenuRequestType" => "NOTIFICATIONS_MENU_REQUEST_TYPE_INBOX"
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            $yt->page->notifSections = $ytdata->actions[0]
                ->openPopupAction->popup->multiPageMenuRenderer->sections;
        });
    }
};