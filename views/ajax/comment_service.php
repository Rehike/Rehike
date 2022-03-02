<?php
// content_html => section.comment-thread-renderer|div.comment-renderer
require "rewriters/CommentThread.php";

use Rewriter\CommentThread;
use \Rehike\Request;
use function YukisCoffee\getPropertyAtPath as getProp;

header("Content-Type: application/json");

// Find action
function findAction()
{
    foreach($_GET as $key => $value)
    {
        if (strpos($key, "action_") > -1)
        {
            return $key;
        }
    }
    // error?
}

$action = findAction();

if (isset($action))
{
    $template = 'ajax/comment_service/' . $action;
    $yt->page = (object) [];
    $yt->comments = (object) [];

    Request::innertubeRequest("page", "next", (object)[
        "continuation" => $_POST['page_token']
    ]);
    $response = Request::getInnertubeResponses()["page"];

    $ytdata = json_decode($response);
}

// Rewrite
const COMMENTS_CONTINUATION_PATH = "onResponseReceivedEndpoints[0].appendContinuationItemsAction";
// Comments header renderer is item 0 in reload response
const COMMENTS_RELOAD_PATH = "onResponseReceivedEndpoints[1].reloadContinuationItemsCommand";
try 
{
    $data = getProp($ytdata, COMMENTS_CONTINUATION_PATH);
}
catch (\YukisCoffee\GetPropertyAtPathException $e)
{
    try
    {
        $data = getProp($ytdata, COMMENTS_RELOAD_PATH);
    }
    catch (\YukisCoffee\GetPropertyAtPathException $e)
    {
        echo json_encode(
            (object)[
                "error" => "Failed to get property at path " . COMMENTS_CONTINUATION_PATH
            ]
        );
        exit();
    }
}

if ("action_get_comment_replies" == $action)
{
    $yt->comments = CommentThread::bakeReplies($data);
}
else if ("action_get_comments" == $action)
{
    $yt->comments = CommentThread::bakeComments($data);
}