<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

Request::innertubeRequest(
    "feed", 
    "browse", 
    (object)[
        "browseId" => "FEwhat_to_watch"
    ],
    "WEB",
    "1.20220303.06.01"
);
$response = Request::getResponses()["feed"];
$yt -> response = $response;

$timeb = round(microtime(true) * 1000);
//echo $timeb - $timea; die();
$ytdata = json_decode($response);
//var_dump( $ytdata);

$shelvesList = $ytdata->contents->twoColumnBrowseResultsRenderer->
    tabs[0]->tabRenderer->content->sectionListRenderer->contents;

$yt->page->continuation = $ytdata->contents->twoColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->continuations[0]->nextContinuationData->continuation;


/*
$shelvesList = $ytdata->contents->singleColumnBrowseResultsRenderer->
   tabs[0]->tabRenderer->content->sectionListRenderer->contents;
   */
   
$yt->page->shelvesList = $shelvesList;