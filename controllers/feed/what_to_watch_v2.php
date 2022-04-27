<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch_v2';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

include "controllers/mixins/guideNotSpfMixin.php";

Request::innertubeRequest(
    "feed", 
    "browse", 
    (object)[
        "browseId" => "FEwhat_to_watch"
    ],
    "WEB",
    "2.20220303.06.01"
);

$response = Request::getInnertubeResponses()["feed"];

$ytdata = json_decode($response);
$yt -> response = $response;
$yt -> videoList = $ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content -> richGridRenderer -> contents;