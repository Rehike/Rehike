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

// begin request
$yt->videoId = $_GET['v'];

require_once('views/utils/extractUtils.php');

Request::innertubeRequest("watch", "next", (object)[
    "videoId" => $_GET["v"]
]);
Request::innertubeRequest("player", "player", (object)[
    "videoId" => $_GET["v"],
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
    ]
]);

$responses = Request::getInnertubeResponses();

$response = $responses["watch"];
$presponse = $responses["player"];

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
$primaryInfo = findKey($results->contents, "videoPrimaryInfoRenderer") ?? null;
$secondaryInfo = findKey($results->contents, "videoSecondaryInfoRenderer") ?? null;
$commentSection = findKey($results->contents, 'itemSectionRenderer') ?? null;

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

// title, viewcount rw
if (!is_null($primaryInfo)) {
    $rw->results = (object) [];
    $rw->results->videoPrimaryInfoRenderer = (object) [];

    $rwp_ = $rw->results->videoPrimaryInfoRenderer;
    $rwp_->title = $primaryInfo->title ?? null;
    $rwp_->viewCount = $primaryInfo->viewCount->videoViewCountRenderer->viewCount ?? null;
    $rwp_->actions = (object) [];
    $rwp_->actions->likeButton = (object) [];
    $rwp_->actions->likeButton->defaultText = ExtractUtils::isolateLikeCnt($primaryInfo->videoActions->menuRenderer
        ->topLevelButtons[0]->toggleButtonRenderer->accessibility->label) ?? null;
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
// description rw
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
}
// recommended rw
if (!is_null($secondaryResults)) {
    $rw->secondaryResults = $secondaryResults;
    // playlist (TODO: disable if playlist)
    
    $autoplayVideo = WatchUtils::getRecomAutoplay($secondaryResults->results, $recomIndex);
    
    $rw->secondaryResults->autoplayRenderer = (object) [
        'results' => [$autoplayVideo]
    ];
    array_splice($rw->secondaryResults->results, $recomIndex, 1);
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
}

$yt->page = $rw;
$yt->rawWatchNextResponse = $response;
$yt->page->title = $_getText($rwp_->title) ?? null;