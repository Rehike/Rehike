<?php
$yt->spfEnabled = true;
$template = 'watch';
$yt->page = (object) [];

include_once($root.'/innertubeHelper.php');

$innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $visitor);
$innertubeBody->videoId = $_GET['v'];
$yticfg = json_encode($innertubeBody);

$apiUrl = 'https://www.youtube.com/youtubei/v1/next?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

$ch = curl_init($apiUrl);

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ['Content-Type: application/json',
    'x-goog-visitor-id: ' . urlencode(encryptVisitorData($visitor))],
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $yticfg,
    CURLOPT_FOLLOWLOCATION => 0,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1
]);

$response = curl_exec($ch);
$ytdata = json_decode($response);

$yt->page->title = $ytdata->contents->twoColumnWatchNextResults->
   results->results->contents[0]->videoPrimaryInfoRenderer->title->
   runs[0]->text;