<?php
$template = "ajax/related";
$yt->page = (object) [];

$yt->spfIdListeners = [
    '@masthead_search<data-is-crosswalk>',
    'watch-more-related'
];

if (!isset($_GET["continuation"])) {
    die("{\"name\":\"other\"}");
}

use \Rehike\Request;

Request::innertubeRequest(
    "related",
    "next",
    (object) [
        "continuation" => $_GET["continuation"]
    ]
);

$response = Request::getInnertubeResponses()["related"];
$ytdata = json_decode($response);

$yt->page->items = $ytdata->onResponseReceivedEndpoints[0]->appendContinuationItemsAction->continuationItems;