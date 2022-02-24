<?php
// content_html => section.comment-thread-renderer|div.comment-renderer
require "rewriters/CommentThread.php";

use Rewriter\CommentThread;
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
    
    include_once($root.'/innertubeHelper.php');
    
    $innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $visitor);
    $innertubeBody->continuation = $_POST['page_token'];
    $yticfg = json_encode($innertubeBody);
    
    $apiUrl = 'https://www.youtube.com/youtubei/v1/next?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';
    
    $ch = curl_init($apiUrl);
    
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => ['Content-Type: application/json',
        'x-goog-visitor-id: ' . urlencode(encryptVisitorData($visitor))],
        CURLOPT_ENCODING => 'gzip',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $yticfg,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
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