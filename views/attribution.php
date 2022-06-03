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
$yt->page->title = $ytdata->contents->twoColumnWatchNextResults->
   results->results->contents[0]->videoPrimaryInfoRenderer->title->
   runs[0]->text;