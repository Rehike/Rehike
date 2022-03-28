<?php
$yt->spfEnabled = false;
$template = 'attribution';
$yt->page = (object) [];

if (!isset($_GET['v'])) {
    echo $twig->render("error/404.twig");
    die();
}

$yt->page->videoId = $_GET['v'];

include_once($root.'/innertubeHelper.php');

use \Rehike\Request;

Request::innertubeRequest(
    "attribution",
    "next",
    (object) [
        "videoId" => $yt->page->videoId
    ]
);
$response = Request::getResponses()["attribution"];

$ytdata = json_decode($response);
$yt->page->title = $ytdata->contents->twoColumnWatchNextResults->
   results->results->contents[0]->videoPrimaryInfoRenderer->title->
   runs[0]->text;