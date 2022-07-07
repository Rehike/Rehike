<?php
/**
 * TODO (kirasicecreamm): This entire system is incredibly hacky
 * and should be replaced in the future (how?)
 */

$template = "ajax/browse";
$yt->page = (object) [];

require "controllers/utils/AndroidW2w15Parser.php";

header("Content-Type: application/json");

if (!isset($_GET["continuation"])) {
    http_response_code(400);
    die("{\"errors\":[\"Invalid Request\"]}");
}

$yt->continuation = $_GET["continuation"];
$yt->target = $_GET["target_id"];

use \Rehike\Request;

// WTW hack
if ($yt->target == "section-list-874807") {
    $response = Request::innertubeRequest(
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ],
        "ANDROID",
        "15.14.33"
    );
} else {
    $response = Request::innertubeRequest(
        "browse",
        (object) [
            "continuation" => $yt->continuation
        ]
    );
}


$yt->response = $response;
$ytdata = json_decode($response);

// Search for next continuation
if ($array = @$ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems)
{
    // Get the last item of the array, then get the name and check if there is a
    // continuationItemRenderer
    if (is_array($array) && ($item = $array[count($array) - 1]))
    foreach ($item as $name => $contents) if ("continuationItemRenderer" == $name)
    {
        $yt->nextContinuation = $contents->continuationEndpoint->continuationCommand->token ?? null;
    }
}
// Android response moment
else if ($item = @$ytdata->continuationContents->sectionListContinuation->continuations[0])
{
    foreach ($item as $name => $contents) if ("nextContinuationData" == $name)
    {
        $yt->nextContinuation = $contents->continuation ?? null;
    }
}

if (isset($ytdata->continuationContents->sectionListContinuation)) {
    $yt->page->shelfList = AndroidW2W15Parser::parse($ytdata->continuationContents->sectionListContinuation->contents);
} else if (isset($ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems)) {
    $head = $ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction;

    // Store the page type
    $yt->continuationPage = $head->targetId ?? "";
    
    $items = $head->continuationItems;

    $videoList = $ytdata->onResponseReceivedActions[0]->appendContinuationItemsAction->continuationItems;

    $newVideoList = [];
    
    for ($i = 0; $i < count($items); $i++)
    {
        if ($content = @$items[$i]->richItemRenderer->content)
        {
            if ("channels-browse-content-grid" == $yt->target)
            {
                foreach ($content as $name => $value)
                {
                    // Convert name formatting
                    // videoRenderer => gridVideoRenderer
                    $name = "grid" . ucfirst($name);

                    $newVideoList[] = (object)[$name => $value];
                    break;
                }
            }
            else
            {
                $newVideoList[] = $content;
            }
        }
        else
        {
            $newVideoList[] = $items[$i];
        }
    }

    $yt->page->lockupList = &$newVideoList;
}