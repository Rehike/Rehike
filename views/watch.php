<?php
use \Rehike\Request;
//error_reporting(0);
require "views/utils/watchUtils.php";
$yt->spfEnabled = true;
$yt->useModularCore = true;
$template = 'watch';
$yt->modularCoreModules = ['www/watch'];
$yt->page = (object) [];

// invalid request redirect
if (!isset($_GET['v'])) {
    header('Location: /');
    die();
}

if(!isset($yt->spf) or $yt->spf == false) {
    require "mod/getGuide.php";
}

// begin request
$yt->videoId = $_GET['v'];
$yt->playlistId = $_GET['list'] ?? null;
$yt->playlistIndex = (string) ((int) ($_GET['index'] ?? '1') - 1);


require_once('views/utils/extractUtils.php');

$watchRequestParams = [
    'videoId' => $yt->videoId
];
if (!is_null($yt->playlistId)) {
    $watchRequestParams['playlistId'] = $yt->playlistId;
    $watchRequestParams['playlistIndex'] = $yt->playlistIndex;
}

if(isset($_GET["t"])) {
    preg_match_all("/\d{1,6}/", $_GET["t"], $times);
    $times = $times[0];
    if (count($times) == 1) { // before you whine "waaahh use case" I CAN'T IT BREAKS IT FOR NO FUCKING REASON, if you wanna make this better, go ahead
        $startTime = (int) $times[0];
    } else if (count($times) == 2) {
        $startTime = ((int) $times[0] * 60) + (int) $times[0];
    } else if (count($times) == 3) {
        $startTime = ((int) $times[0] * 3600) + ((int) $times[1] * 60) + (int) $times[2];
    } else {
        $startTime = 0;
    }
}

Request::queueInnertubeRequest("watch", "next", (object) $watchRequestParams);
Request::queueInnertubeRequest("player", "player", (object) ([
    "playbackContext" => [
        'contentPlaybackContext' => (object) [
            'autoCaptionsDefaultOn' => false,
            'autonavState' => 'STATE_OFF',
            'html5Preference' => 'HTML5_PREF_WANTS',
            'lactMilliseconds' => '13407',
            'mdxContext' => (object) [],
            'playerHeightPixels' => 1080,
            'playerWidthPixels' => 1920,
            'signatureTimestamp' => $yt->playerCore->sts
        ]   
    ],
    "startTimeSecs" => $startTime ?? 0
] + $watchRequestParams));

$ch = curl_init("https://returnyoutubedislikeapi.com/votes?videoId=" . $yt->videoId);
curl_setopt_array($ch, [
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1
]);
$rydResponse = curl_exec($ch);
curl_close($ch);
$dislikesData = json_decode($rydResponse);


$responses = Request::getResponses();

$response = $responses["watch"];
$presponse = $responses["player"];
$yt->response = $response;

$ytdata = json_decode($response);
$playerResponse = json_decode($presponse);
$yt->playerResponse = $playerResponse;
// remove ads lol
if (isset($yt->playerResponse->playerAds)) unset($yt->playerResponse->playerAds);
if (isset($yt->playerResponse->adPlacements)) unset($yt->playerResponse->adPlacements);
// */

// end request

/*

$contents = $ytdata->contents ?? null;
$results = $contents->twoColumnWatchNextResults->results->results ?? null;
$secondaryResults = $contents->twoColumnWatchNextResults->secondaryResults->secondaryResults ?? null;
$yt->page->results = $results;
$yt->page->secondaryResults = $secondaryResults;
$yt->page->primaryInfo = findKey($results->contents, "videoPrimaryInfoRenderer") ?? null;
$yt->page->secondaryInfo = findKey($results->contents, "videoSecondaryInfoRenderer") ?? null;

// TODO: migrate this to a function soon
$yt->temp = $yt->temp ?? (object) [];
$yt->temp->likeCount = preg_replace(
    '/(like this video along with )|( other people)/', 
    '', 
    $yt->page->primaryInfo->videoActions->menuRenderer
        ->topLevelButtons[0]->toggleButtonRenderer->accessibility->label
) ?? null;

$yt->page->title = $_getText($yt->page->primaryInfo->title);
*/

$contents = $ytdata->contents ?? null;
$results = $contents->twoColumnWatchNextResults->results->results ?? null;
$secondaryResults = $contents->twoColumnWatchNextResults->secondaryResults->secondaryResults ?? null;
$playlist = $contents->twoColumnWatchNextResults->playlist ?? null;
$primaryInfo = findKey($results->contents, "videoPrimaryInfoRenderer") ?? null;
$secondaryInfo = findKey($results->contents, "videoSecondaryInfoRenderer") ?? null;

/**
 * PATCH (yukiscoffee): Move comment section model to custom function
 * for unique detection mechanism.
 * 
 * Previous mechanism failed to select the right property if more
 * than one itemSectionRenderer was present in the contents model.
 */
$commentSection = WatchUtils::findCommentsSection($results->contents) ?? null;

/*
$rw = (object) [
    'results' => (object) [
        'videoPrimaryInfoRenderer' => (object) [
            'title' => null,
            'viewCount' => null,
            'owner' => (object) [
                'title' => null,
                'thumbnail' => null,
                'badges' => null,
                'navigationEndpoint' => null,
                'subscriptionButton' => (object) [
                    'isDisabled' => null,
                    'subscribed' => null,
                    'subscriberCountText' => null,
                    'shortSubscriberCountText' => null,
                    'type' => null,
                ]
            ],
            'actions' => (object) [
                'likeButton' => (object) [
                    'defaultText' => null
                ]
            ]
        ],
        'videoSecondaryInfoRenderer' => (object) [
            'description' => null,
            'dateText' => null,
            'metadataRowContainer' => (object) [
                'items' => null
            ],
            'showMoreText' => null,
            'showLessText' => null
        ],
        'videoDiscussionRenderer' => (object) [
            'continuation' => null
        ]
    ],
    'secondaryResults' => (object) []
];
*/
$rw = (object) [];

/**
 * Title, viewcount RW
 */
if (!is_null($primaryInfo)) {
    $rw->results = (object) [];
    $rw->results->videoPrimaryInfoRenderer = (object) [];

    $rwp_ = $rw->results->videoPrimaryInfoRenderer;
    $rwp_->title = $primaryInfo->title ?? null;
    $rwp_->hashtags = [];
    for ($i = 0; $i < count($rwp_->title->runs); $i++) {
        if (isset($rwp_->title->runs[$i]->navigationEndpoint->browseEndpoint->browseId) and $rwp_->title->runs[$i]->navigationEndpoint->browseEndpoint->browseId == "FEhashtag") {
            $rwp_->hashtags[] = preg_replace("/#/", "", $rwp_->title->runs[$i]->text);
        } 
    }
    $rwp_->superTitle = isset($primaryInfo->superTitleLink) ? (object) [] : null;
    if (isset($rwp_->superTitle)) {
        $rwp_->superTitle->text = preg_replace("/For/", "for", preg_replace("/On/", "on", ucwords(strtolower($_getText($primaryInfo->superTitleLink)))));
        $rwp_->superTitle->url = $_getUrl($primaryInfo->superTitleLink->runs[0]);
    }
    $rwp_->viewCount = $primaryInfo->viewCount->videoViewCountRenderer->viewCount ?? null;
    $rwp_->badges = $primaryInfo->badges ?? null;
    $rwp_->actions = (object) [];
    $rwp_->actions->likeButton = (object) [];
    $rwp_->actions->likeButton->defaultText = ExtractUtils::isolateLikeCnt($primaryInfo->videoActions->menuRenderer
        ->topLevelButtons[0]->toggleButtonRenderer->accessibility->label) ?? null;
    if (isset($rwp_->actions->likeButton->defaultText) and $rwp_->actions->likeButton->defaultText != '') {
        $yt->ratingsEnabled = true;
        $likeCount = (int) str_replace(",", "", $rwp_->actions->likeButton->defaultText);
        $rwp_->dislikeCount = $dislikesData -> dislikes;
        if ($likeCount + $rwp_->dislikeCount != 0) {
            $rwp_->likePercent = ($likeCount / ($likeCount + $rwp_->dislikeCount)) * 100;
            $rwp_->dislikePercent = 100 - $rwp_->likePercent;
        } else {
            $rwp_->likePercent = 50;
            $rwp_->dislikePercent = 50;
        }
    }


    // owner info rw
    $mpo_ = $secondaryInfo->owner->videoOwnerRenderer ?? null;

    $rwp_->owner = (object) [];
    $rwpo_ = $rwp_->owner;
    $rwpo_->title = $mpo_->title ?? null;
    $rwpo_->thumbnail = $mpo_->thumbnail ?? null;
    $rwpo_->badges = $mpo_->badges ?? null;
    $rwpo_->navigationEndpoint = $mpo_->navigationEndpoint ?? null;
    // subscribe button rw
    $ms_ = $secondaryInfo->subscribeButton ?? null;
    if (isset($ms_->buttonRenderer)) { // logged out
        $ms_ = $secondaryInfo->subscribeButton->buttonRenderer;

        $rwpo_->subscriptionButton = (object) [];
        $rwpos_ = $rwpo_->subscriptionButton;
        $rwpos_->isDisabled = $ms_->isDisabled;
        $rwpos_->subscriberCountText = isset($mpo_->subscriberCountText) 
            ? ExtractUtils::isolateSubCnt($_getText($mpo_->subscriberCountText)) 
            : null;
        $rwpos_->shortSubscriberCountText = $rwpos_->subscriberCountText;
        $rwpos_->isSubscribed = false;
        $rwpos_->type = 'FREE';
    } else if (isset($ms->subscribeButtonRenderer)) {
        // TODO: logged in rw
    }
}
/**
 * Description RW
 */
if (!is_null($secondaryInfo)) {
    $rw->results->videoSecondaryInfoRenderer = (object) [];

    $rws_ = $rw->results->videoSecondaryInfoRenderer;
    $rws_->description = $secondaryInfo->description ?? null;
    $rws_->dateText = isset($primaryInfo->dateText)
        ? ExtractUtils::resolveDate($primaryInfo->dateText)
        : null;
    $rws_->metadataRowContainer = (object) [];
    $rws_->metadataRowContainer->items = $secondaryInfo->metadataRowContainer
        ->metadataRowContainerRenderer->rows ?? null;
    $rws_->showMoreText = $secondaryInfo->showMoreText ?? null;
    $rws_->showLessText = $secondaryInfo->showLessText ?? null;
    $rws_->metaItems = $secondaryInfo->metadataRowContainer->metadataRowContainerRenderer->rows ?? null;
}
/**
 * Recommended RW
 */
if (!is_null($secondaryResults)) {
    $rw->secondaryResults = $secondaryResults;
    
    // disable autoplay video if playlist
    if (is_null($playlist)) {
        $autoplayVideo = WatchUtils::getRecomAutoplay($secondaryResults->results, $recomIndex);
        
        $rw->secondaryResults->autoplayRenderer = (object) [
            'results' => [$autoplayVideo]
        ];
        array_splice($rw->secondaryResults->results, $recomIndex, 1);
    }
}

/**
 * Playlist RW
 */
if (!is_null($playlist)) {
    $playlist = $playlist->playlist;
    $rw->playlist = $playlist;
    
    // count text needs a little work
    $_oplr = $playlist->videoCountText->runs; // original playlist runs
    $plCurIndex = $_oplr[0]->text;
    $plVideoCount = $_oplr[2]->text;

    if ($plVideoCount == '1') {
        $plVideoCount = '1 video';
    } else {
        $plVideoCount .= ' videos';
    }

    $rw->playlist->videoCountText = (object) [
        'currentIndex' => $plCurIndex,
        'videoCount' => $plVideoCount
    ];

    // previous/next video ids also need a little work
    // let's just catch two cases with one
    $curIndInt = $playlist->localCurrentIndex;
    $plPrevId = $playlist->contents[$curIndInt - 1]->playlistPanelVideoRenderer->videoId ?? null;
    $plPrevUrl = '/watch?v=' . $plPrevId . '&index=' . (string) ($curIndInt - 1) . '&list=' . $yt->playlistId;
    $plNextId = $playlist->contents[$curIndInt + 1]->playlistPanelVideoRenderer->videoId ?? null;
    $plNextUrl = '/watch?v=' . $plNextId . '&index=' . (string) ($curIndInt + 1) . '&list=' . $yt->playlistId;

    $rw->playlist->previousVideo = [
        'id' => $plPrevId,
        'url' => $plPrevUrl
    ];
    $rw->playlist->nextVideo = [
        'id' => $plNextId,
        'url' => $plNextUrl
    ];
}

/**
 * Comments RW
 */
if (!is_null($commentSection) && isset($commentSection->contents[0]->continuationItemRenderer)) {
    $yt->commentsToken = $commentSection->contents[0]->continuationItemRenderer
        ->continuationEndpoint->continuationCommand->token;
    $rw->results->videoDiscussionRenderer = (object) [
        'continuation' => $yt->commentsToken
    ];
} else if (!is_null($commentSection) && isset($commentSection->contents[0]->messageRenderer)) {
    $rw->results->videoDiscussionNotice = (object) [
        'message' => $commentSection->contents[0]->messageRenderer->text
    ];
}

$yt->page = $rw;
$yt->rawWatchNextResponse = $response;
$yt->page->title = $_getText($rwp_->title) ?? null;