<?php
use \Rehike\Request;

$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'results';
$yt->modularCoreModules = ['www/results'];
$yt->page = (object) [];

// invalid request redirect
if (!isset($_GET['search_query'])) {
    header('Location: /');
    die();
}

include "controllers/mixins/guideNotSpfMixin.php";

$yt->searchQuery = $_GET['search_query'];

Request::innertubeRequest("page", "search", (object)[
    "query" => $_GET['search_query']
]);
$response = Request::getInnertubeResponses()["page"];

$ytdata = json_decode($response);
$resultsList = $ytdata->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents[0]->itemSectionRenderer->contents;
$yt->page->resultsList = $resultsList;
$yt->response = $response;
$yt->resultCount = $ytdata->estimatedResults;