<?php
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'feed/what_to_watch';
$yt->modularCoreModules = ['www/feed'];
$yt->page = (object) [];
$yt->enableFooterCopyright = true;

include_once($root.'/innertubeHelper.php');

$innertubeBody = generateInnertubeInfoBase('WEB', '1.20200101.01.01', $visitor);
$innertubeBody->browseId = 'FEwhat_to_watch';
$yticfg = json_encode($innertubeBody);

$apiUrl = 'https://www.youtube.com/youtubei/v1/browse?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8';

//$yticfg = '{"context":{"client":{"hl":"en","gl":"US","remoteHost":"72.211.166.57","deviceMake":"","deviceModel":"","visitorData":"CgtRUmUwTG1tRUp5WSjay52GBg%3D%3D","userAgent":"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0,gzip(gfe)","clientName":"WEB","clientVersion":"1.20210519.01.00","osName":"Windows","osVersion":"10.0","originalUrl":"https://www.youtube.com/","platform":"DESKTOP","clientFormFactor":"UNKNOWN_FORM_FACTOR","timeZone":"America/Phoenix","browserName":"Firefox","browserVersion":"89.0","screenWidthPoints":1574,"screenHeightPoints":661,"screenPixelDensity":1,"screenDensityFloat":1,"utcOffsetMinutes":-420,"userInterfaceTheme":"USER_INTERFACE_THEME_LIGHT","mainAppWebInfo":{"graftUrl":"/","webDisplayMode":"WEB_DISPLAY_MODE_BROWSER","isWebNativeShareAvailable":false}},"user":{"lockedSafetyMode":false},"request":{"useSsl":true,"internalExperimentFlags":[],"consistencyTokenJars":[]},"clickTracking":{"clickTrackingParams":"CBkQsV4iEwiowZ6PqZfxAhWHRUwIHVTBCp0="},"adSignalsInfo":{"params":[{"key":"dt","value":"1623680473293"},{"key":"flash","value":"0"},{"key":"frm","value":"0"},{"key":"u_tz","value":"-420"},{"key":"u_his","value":"2"},{"key":"u_java","value":"false"},{"key":"u_h","value":"1440"},{"key":"u_w","value":"2560"},{"key":"u_ah","value":"1400"},{"key":"u_aw","value":"2560"},{"key":"u_cd","value":"24"},{"key":"u_nplug","value":"0"},{"key":"u_nmime","value":"0"},{"key":"bc","value":"31"},{"key":"bih","value":"661"},{"key":"biw","value":"1557"},{"key":"brdim","value":"609,201,609,201,2560,0,1586,1115,1574,661"},{"key":"vis","value":"1"},{"key":"wgl","value":"true"},{"key":"ca_type","value":"image"}]}},"browseId":"FEwhat_to_watch"}';

//$yticfg = '{"context":{"client":{"hl":"en","gl":"US","remoteHost":"72.211.166.57","deviceMake":"","deviceModel":"","visitorData":"CgtRUmUwTG1tRUp5WSjZtsKLBg%3D%3D","userAgent":"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36,gzip(gfe)","clientName":"WEB","clientVersion":"1.20200101.01.01","osName":"Windows","osVersion":"10.0","originalUrl":"https://www.youtube.com/reporthistory?v3feed=wHaT_2_wAtCh&amp;v3cv=1&amp;app=true","platform":"DESKTOP","clientFormFactor":"UNKNOWN_FORM_FACTOR","userInterfaceTheme":"USER_INTERFACE_THEME_LIGHT","timeZone":"America/Phoenix","browserName":"Chrome","browserVersion":"94.0.4606.81","screenWidthPoints":150,"screenHeightPoints":903,"screenPixelDensity":1,"screenDensityFloat":1,"utcOffsetMinutes":-420,"mainAppWebInfo":{"graftUrl":"https://www.youtube.com/?v3feed=wHaT_2_wAtCh","webDisplayMode":"WEB_DISPLAY_MODE_BROWSER","isWebNativeShareAvailable":false}},"user":{"lockedSafetyMode":false},"request":{"useSsl":true,"internalExperimentFlags":[],"consistencyTokenJars":[]},"clickTracking":{"clickTrackingParams":"IhMIpqDau4na8wIVhUNMCB2z6A/w"},"adSignalsInfo":{"params":[{"key":"dt","value":"1623439544078"},{"key":"flash","value":"0"},{"key":"frm","value":"0"},{"key":"u_tz","value":"-240"},{"key":"u_his","value":"6"},{"key":"u_java","value":"false"},{"key":"u_h","value":"1080"},{"key":"u_w","value":"1920"},{"key":"u_ah","value":"1040"},{"key":"u_aw","value":"1920"},{"key":"u_cd","value":"24"},{"key":"u_nplug","value":"0"},{"key":"u_nmime","value":"0"},{"key":"bc","value":"31"},{"key":"bih","value":"938"},{"key":"biw","value":"1403"},{"key":"brdim","value":"-8,-8,-8,-8,1920,0,1936,1056,1420,938"},{"key":"vis","value":"1"},{"key":"wgl","value":"true"},{"key":"ca_type","value":"image"}]}},"browseId":"FEwhat_to_watch"}';

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

$shelvesList = $ytdata->contents->twoColumnBrowseResultsRenderer->
    tabs[0]->tabRenderer->content->sectionListRenderer->contents;


/*
$shelvesList = $ytdata->contents->singleColumnBrowseResultsRenderer->
   tabs[0]->tabRenderer->content->sectionListRenderer->contents;
   */
   
$yt->page->shelvesList = $shelvesList;

curl_close($ch);