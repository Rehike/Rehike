<?php
$template = "ajax/browse";
$yt->page = (object) [];

header("Content-Type: application/json");

if (!isset($_GET["continuation"])) {
    http_response_code(400);
    die("{\"errors\":[\"Invalid Request\"]}");
}

$yt->continuation = $_GET["continuation"];
$yt->target = $_GET["target_id"];

if ($yt->target == "section-list-874807") {
    $yt->reqVer = "1.20220303.06.01";
} else {
    $yt->reqVer = "2.20220303.01.01";
}

use \Rehike\Request;

if ($yt->target == "section-list-874807") {
    Request::innertubeRequest(
        "browse",
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ],
        "WEB",
        "1.20220303.06.01"
    );
} else {
    Request::innertubeRequest(
        "browse",
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ]
    );
}

$response = Request::getInnertubeResponses()["browse"];
$yt->response = $response;
$ytdata = json_decode($response);

if (isset($ytdata->continuationContents->sectionListContinuation)) {
    $yt->page->shelfList = $ytdata->continuationContents->sectionListContinuation->contents;
} else if (isset($ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems)) {
    $yt->page->lockupList = $ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems;
}