<?php
use \Rehike\Request;

require "views/utils/AndroidW2w15Parser.php";

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

/**
 * BUG (yukiscoffee): WEB what_to_watch shelves API is
 * down (permanently?). However, ANDROID shelves have
 * a similar markup and are still up.
 */

Request::innertubeRequest(
    "feed", 
    "browse", 
    (object)[
        "browseId" => "FEwhat_to_watch"
    ],
    "ANDROID",
    "15.14.33"
);
$response = Request::getInnertubeResponses()["feed"];
$yt -> response = $response;

$timeb = round(microtime(true) * 1000);
//echo $timeb - $timea; die();
$ytdata = json_decode($response);
//var_dump( $ytdata);

$shelvesList = $ytdata->contents->singleColumnBrowseResultsRenderer->
    tabs[0]->tabRenderer->content->sectionListRenderer->contents;


/** Continuations are still buggy */
$yt->page->continuation = $ytdata->contents->singleColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->continuations[0]->nextContinuationData->continuation;

$shelvesList = AndroidW2w15Parser::parse($shelvesList);

/*
$shelvesList = $ytdata->contents->singleColumnBrowseResultsRenderer->
   tabs[0]->tabRenderer->content->sectionListRenderer->contents;
   */
   
$yt->page->shelvesList = $shelvesList;