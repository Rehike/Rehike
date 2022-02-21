<?php
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

$yt->page->title = $_GET['search_query'];

include_once($root.'/innertubeHelper.php');

$innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $visitor);
$innertubeBody->query = $_GET['search_query'];
$yticfg = json_encode($innertubeBody);

$apiUrl = 'https://www.youtube.com/youtubei/v1/search?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

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

$ytdata = json_decode($response);

$resultsList = $ytdata->contents->twoColumnSearchResultsRenderer->primaryContents->sectionListRenderer->contents[0]->itemSectionRenderer->contents;


$yt->page->resultsList = $resultsList;

$yt->response = $response;

curl_close($ch);