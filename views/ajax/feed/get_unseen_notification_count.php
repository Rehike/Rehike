<?php
    $template = "ajax/feed/get_unseen_notification_count";
    $yt->page = (object) [];

    use \Rehike\Request;

    Request::innertubeRequest(
        "count",
        "notification/get_unseen_count"
    );
    $response = Request::getResponses()["count"];
    $ytdata = json_decode($response);

    $yt->page->count = $ytdata->actions[0]->updateNotificationsUnseenCountAction->unseenCount ?? null;
    $yt->page->pollingTimeout = $ytdata->actions[0]->updateNotificationsUnseenCountAction->unseenCount ?? null;