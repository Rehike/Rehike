<?php
$template = 'common/watch/watch_fragments2/comments';
$yt->page = (object) [];
$yt->comments = (object) [];
$yt->commentsHeader = (object) [];

$yt->spfIdListeners = [
    '@masthead_search<data-is-crosswalk>',
    'watch-discussion'
];
$yt->spfName = 'other';

include_once($root.'/innertubeHelper.php');

$innertubeBody = generateInnertubeInfoBase('WEB', '2.20200101.01.01', $visitor);
$innertubeBody->continuation = $_GET['ctoken'];
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

/**
 *  Comments Header Rewrite
 * 
 *  commentsHeader: {
 *      "title": "Comments" title,
 *      "commentsCountText": Number of comments,
 *      "sortRenderer": Object containing sort options and state {
 *          "title": Selected item's title,
 *          "items": Array of items [ =>
 *              "title": Title of the menu item,
 *              "selected": Boolean specifiying item selection status,
 *              "continuation": Continuation token of the sort option
 *          ]
 *      },
 *      "simpleBoxRenderer": Object encoding simplebox renderer {
 *          "authorThumbnail": Thumbnails array of the author avatar.
 *          "placeholderText": Placeholder text displayed when inactive.
 *      }
 *  }
 */
const RLC = 'reloadContinuationItemsCommand'; // shorthand
const ORRE = 'onResponseReceivedEndpoints'; // shorthand
const CI = 'continuationItems'; // shorthand

// comments header shorthand
$_och = $ytdata->{ORRE}[0]->{RLC}->{CI}[0]->commentsHeaderRenderer; // original comments header
$_ch = $yt->commentsHeader; // comments header shorthand

// commentsHeader.title:
// commentsHeader.commentsCountText:
if ($a = $_och->countText) {
    $a = $a->runs;
    $_ch->title = $a[1]->text; // hitchhiker comments section reversed title and count
    $_ch->commentsCountText = $a[0]->text;
}

// commentsHeader.sortRenderer:
if ($a = $_och->sortMenu) {
    $a = $a->sortFilterSubMenuRenderer->subMenuItems; // everything we need in here...
    $_ch->sortRenderer = (object) [];
    $_sr = $_ch->sortRenderer; // shorthand
    for ($i = 0; $i < count($a); $i++) {
        $item = $a[$i];

        if ($item->selected) {
            $_sr->title = $item->title;
        }

        $_sr->items[$i] = (object) [];
        $_sri = $_sr->items[$i]; // shorthand
        
        $_sri->title = $item->title;
        $_sri->selected = $item->selected;
        $_sri->continuation = $item->serviceEndpoint->continuationCommand->token;

        // just in case, probably won't do much harm
        $_sri->menuName = (function() use ($i){
            switch($i) {
                case 0: return 'top-comments';
                case 1: return 'newest-first';
            }
        })();
    }
}

// commentsHeader.simpleBoxRenderer:
if ($a = ($_och->createRenderer->commentSimpleboxRenderer ?? false)) {
    $_ch->simpleBoxRenderer = (object) [];
    $_sbr = $_ch->simpleBoxRenderer; // shorthand
    $_sbr->authorThumbnail = $a->authorThumbnail;
    $_sbr->placeholderText = $_getText($a->placeholderText);
}
/**
 * Comments Threads Rewrite
 * TODO: further rewrite may be necessary
 */
$_oct = $ytdata->{ORRE}[1]->{RLC}->{CI}; // original comment threads
$yt->comments = $_oct;

foreach ($yt->comments as $index => $comment) if (isset($comment->commentThreadRenderer->comment->commentRenderer->voteCount))
{
    $likeButtonLabel = $comment->commentThreadRenderer->comment->commentRenderer->actionButtons
        ->commentActionButtonsRenderer->likeButton->toggleButtonRenderer->accessibilityData
        ->accessibilityData->label;
    $comment->commentThreadRenderer->comment->commentRenderer->voteCount = (function ($label)
        {
            return preg_replace("/(Like this comment along with )|(,)|( other person)|(other people)/", "", $label);
        }
    )($likeButtonLabel);
}

//header('content-type: application/json'); echo json_encode($yt); die(); // debug