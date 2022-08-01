<?php
/**
 * Temporary legacy controller v1 (CV1) ending script.
 * 
 * This will be removed following a complete migration to
 * the Controller V2 system and the deprecation of V1.
 * 
 * Until then, this ugly file's gotta remain in the root. Sorry :(
 */
use Rehike\Model\Footer\MFooter as Footer;

// lazy spf check
if (isset($_GET['spf'])) {
    $yt->spf = true;
    $__spfState = $_GET['spf'];
    $__spfUrl = preg_replace('/.spf='.$_GET['spf'].'/', '', $_SERVER['REQUEST_URI']);
}

$yt->url = $__spfUrl ?? $_SERVER['REQUEST_URI'];

if (isset($_COOKIE['wide'])) {
    $yt -> theaterMode = $_COOKIE['wide'];
} else {
    $yt -> theaterMode = "0";
    $_COOKIE['wide'] = "0";
}

// i can't fucking believe i STILL have to modify this file
$yt -> footer = new Footer();

//$yt->spfEnabled = false; // DEBUG
echo $twig->render($template . '.twig', [$yt]);
$timec = round(microtime(true) * 1000);
//ob_end_clean(); echo $timec - $timeb; die();

use \SpfPhp\SpfPhp;
const SPF_NAV = 'navigate';
const SPF_NB = 'navigate-back';
const SPF_NF = 'navigate-forward';
const SPF_LOAD = 'load';
if (isset($__spfState) && 
    ($__spfState == SPF_NAV ||
     $__spfState == SPF_NB ||
     $__spfState == SPF_NF)
) {
    $yt->spfIdListeners = [
        '@body<class>',
        'player-unavailable<class>',
        'debug',
        'early-body',
        'appbar-content<class>',
        'alerts',
        'content',
        '@page<class>',
        'header',
        'ticker-content',
        'player-playlist<class>',
        '@player<class>'
    ];
    $yt->spfUrl = $__spfUrl;
}
if (isset($yt->spf) && $yt->spf && http_response_code() == 200) { // isset to prevent warning; http_response_code to prevent broken spf 404 page
    $_htmlBuffer = ob_get_clean();
    header('Content-Type: application/json');

    $spfResponse = @SpfPhp::parse(
        $_htmlBuffer,
        $yt->spfIdListeners,
        (object) [
            'skipSerialisation' => true
        ]
    );
    if (isset($yt->spfUrl)) $spfResponse->url = $yt->spfUrl;
    if (isset($yt->spfName)) $spfResponse->name = $yt->spfName;

    if (isset($yt->playerResponse)) {
        $spfResponse->data = (object) ['swfcfg' => (object) ['args' => (object) [
            'raw_player_response' => null,
            'raw_watch_next_response' => null
        ]]];
        $spfResponse->data->swfcfg->args->raw_player_response = $yt->playerResponse;
        $spfResponse->data->swfcfg->args->raw_watch_next_response = json_decode($yt->rawWatchNextResponse);

        if (isset($yt->page->playlist)) {
            $spfResponse->data->swfcfg->args->is_listed = '1';
            $spfResponse->data->swfcfg->args->list = $yt->playlistId;
            $spfResponse->data->swfcfg->args->videoId = $yt->videoId;
        }
    }

    echo json_encode($spfResponse);
} else {
    ob_end_flush();
}