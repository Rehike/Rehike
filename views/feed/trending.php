<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/trending';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

$response = Request::innertubeRequest("browse", (object)[
    "browseId" => "FEtrending"
]);

$timeb = round(microtime(true) * 1000);
//echo $timeb - $timea; die();
$ytdata = json_decode($response);
//var_dump( $ytdata);

$yt->page->data = $response;

$shelvesList = $ytdata->contents->twoColumnBrowseResultsRenderer->
    tabs[0]->tabRenderer->content->sectionListRenderer->contents;

$yt->page->shelvesList = $shelvesList;