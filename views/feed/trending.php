<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/trending';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

include_once($root.'/innertubeHelper.php');

$innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $visitor);
$innertubeBody->browseId = 'FEtrending';
$yticfg = json_encode($innertubeBody);

$apiUrl = 'https://www.youtube.com/youtubei/v1/browse?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

$ch = curl_init($apiUrl);

$timea = round(microtime(true) * 1000);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    'x-goog-visitor-id: ' . urlencode(encryptVisitorData($visitor))],
    CURLOPT_ENCODING => 'gzip',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $yticfg,
    CURLOPT_FOLLOWLOCATION => 0,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1
]);

$response = curl_exec($ch);
$timeb = round(microtime(true) * 1000);
//echo $timeb - $timea; die();
$ytdata = json_decode($response);
//var_dump( $ytdata);

$yt->page->data = $response;

$shelvesList = $ytdata->contents->twoColumnBrowseResultsRenderer->
    tabs[0]->tabRenderer->content->sectionListRenderer->contents;

$yt->page->shelvesList = $shelvesList;

curl_close($ch);