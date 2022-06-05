<?php
use \Rehike\Request;

$yt->spfEnabled = false;
$template = 'attribution';
$yt->page = (object) [];
$yt->page->videoId = $_GET['v'];

Request::innertubeRequest("page", "next", (object)[
    "videoId" => $_GET['v']
]);
$response = Request::getInnertubeResponses()["page"];

$ytdata = json_decode($response);
$primaryInfo = findKey($ytdata->contents->twoColumnWatchNextResults->results->results->contents, "videoPrimaryInfoRenderer") ?? null;

$yt->page->title = $primaryInfo->title ?? null;