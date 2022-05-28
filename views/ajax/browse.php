<?php
$template = "ajax/browse";
$yt->page = (object) [];

require "views/utils/AndroidW2w15Parser.php";

header("Content-Type: application/json");

if (!isset($_GET["continuation"])) {
    http_response_code(400);
    die("{\"errors\":[\"Invalid Request\"]}");
}

$yt->continuation = $_GET["continuation"];
$yt->target = $_GET["target_id"];

use \Rehike\Request;

if ($yt->target == "section-list-874807") {
    Request::innertubeRequest(
        "browse",
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ],
        "ANDROID",
        "15.14.33"
    );

    $response = Request::getInnertubeResponses()["browse"];
} else {
    Request::innertubeRequest(
        "browse",
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ]
    );

    $response = Request::getInnertubeResponses()["browse"];
}


$yt->response = $response;
$ytdata = json_decode($response);

if (isset($ytdata->continuationContents->sectionListContinuation)) {
    $yt->page->shelfList = AndroidW2W15Parser::parse($ytdata->continuationContents->sectionListContinuation->contents);
} else if (isset($ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems)) {
    $yt->page->lockupList = $ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems;
}