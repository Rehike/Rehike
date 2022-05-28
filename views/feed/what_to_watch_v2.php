<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch_v2';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;
$yt->flow = (isset($_GET["flow"]) and $_GET["flow"] == "2") ? "list" : "grid";

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

Request::innertubeRequest(
    "feed", 
    "browse", 
    (object)[
        "browseId" => "FEwhat_to_watch"
    ]
);

$response = Request::getInnertubeResponses()["feed"];

$ytdata = json_decode($response);
$yt -> response = $response;
$yt -> videoList = $ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content -> richGridRenderer -> contents;
$yt -> page -> continuation = end($yt -> videoList) -> continuationItemRenderer -> continuationEndpoint -> continuationCommand -> token ?? null;