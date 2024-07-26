<?php
namespace Rehike\Model\Watch\Watch8;

use Rehike\Model\Watch\WatchModel as WatchBase;
use Rehike\Model\Watch\Watch7\MVideoDiscussionDelayloadRenderer;
use Rehike\Model\Watch\Watch7\MVideoDiscussionNotice;
use Rehike\Model\Watch\Watch7\MCreatorBar;
use Rehike\i18n\i18n;
use Rehike\ConfigManager\Config;
use Rehike\Util\PrefUtils;
use Rehike\Signin\API as SignIn;
use Rehike\Model\Browse\InnertubeBrowseConverter;
use Rehike\Model\Common\MButton;
use Rehike\Model\Traits\NavigationEndpoint;
use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Model\Comments\CommentThread;
use Rehike\Model\Comments\CommentsHeader;

use function Rehike\Async\async;

/**
 * Implements the watch8 subcontroller for the watch model
 * implementation.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Watch8Subcontroller
{
    /**
     * Called from the main watch model
     * 
     * @param object $data
     * @return object[]
     */
    public static function bakeResults(&$data, $videoId)
    {
        return async(function () use (&$data, $videoId)
        {
            // Create references
            $primaryInfo = &WatchBase::$primaryInfo;
            $secondaryInfo = &WatchBase::$secondaryInfo;
            $commentSection = &WatchBase::$commentSection;

            $results = [];

            // Push creator bar if the video is yours
            if (WatchBase::$isOwner)
            {
                $results[] = (object) [
                    "videoCreatorBarRenderer" => new MCreatorBar($videoId)
                ];
            }

            // Push primary info (if it exists)
            if (!is_null($primaryInfo)) $results[] = (object)[
                "videoPrimaryInfoRenderer" => new MVideoPrimaryInfoRenderer(WatchBase::class, $videoId)
            ];

            // Push secondary info (if it exists)
            if (!is_null($secondaryInfo)) $results[] = (object)[
                "videoSecondaryInfoRenderer" => new MVideoSecondaryInfoRenderer(WatchBase::class)
            ];

            // Push comments (if they exist)
            if (!is_null($commentSection))
            {
                // In order to determine the type of the comment section renderer,
                // we compare the type of the first item. If it's not a special type,
                // then we just parse the whole contents.
                $commentContents = @$commentSection->contents;
                $firstItem = @$commentContents[0];

                // NOTICE (kirasicecreamm): As of 2023/09/15 or thereabout, comments
                // may just be embedded in the response.
                if (isset($firstItem->continuationItemRenderer))
                {
                    // If the comment section exists, create a video
                    // discussion renderer that contains its continuation.

                    $continuationToken = $firstItem->continuationItemRenderer
                        ->continuationEndpoint->continuationCommand->token;
                    
                    // Push the continuation token to yt global
                    WatchBase::$yt->commentsToken = $continuationToken;

                    $results[] = (object)[
                        "videoDiscussionDelayloadRenderer" => new MVideoDiscussionDelayloadRenderer(
                            $continuationToken
                        )
                    ];
                }
                else if (isset($firstItem->messageRenderer))
                {
                    // If the comment section renderer contains a message,
                    // create a videoDiscussionNotice instead.
                    $message = $firstItem->messageRenderer->text;

                    $results[] = (object)[
                        "videoDiscussionNotice" => new MVideoDiscussionNotice($message)
                    ];
                }
                else if (isset($commentContents))
                {
                    // In this case, the comments are baked into the response rather
                    // than delayloaded.
                    $videoId = WatchBase::$yt->videoId ?? $_GET["v"];

                    $headerRenderer = CommentsHeader::fromData(
                        $commentSection?->header[0]?->commentsHeaderRenderer
                    );

                    $commentsBakery = new CommentThread(WatchBase::$response);
                    $contentsRenderer = yield $commentsBakery->bakeComments($commentContents);

                    $results[] = (object)[
                        "videoDiscussionRenderer" => (object)[
                            "headerRenderer" => $headerRenderer,
                            "comments" => $contentsRenderer
                        ]
                    ];
                }
            }

            return $results;
        });
    }

    /**
     * Called from main watch model
     * 
     * This performs check for autoplay and moves the video
     * to its respective position if so.
     * 
     * @param object $data
     * @return object
     */
    public static function bakeSecondaryResults(&$data)
    {
        $yt = &WatchBase::$yt;
        // Get data from the reference in the datahost
        $origResults = &WatchBase::$secondaryResults;
        $response = [];
        $i18n = i18n::getNamespace("watch");

        if (isset($origResults->results))
        {
            $secondaryResults = $origResults;

            /*
             * FIX (kirasicecreamm): Detection cannot rely purely upon assumption that the renderer
             * exists based on login status. It's required to perform a more sophisticated approach
             * when an item section renderer is not used to render the recommendations.
             * 
             * Other than that, I made a silly mistake here and put this inside of the
             * autoplay condition below, which prevented it from displaying on playlists, as they
             * lack the autoplay condition.
             */
            if (isset($secondaryResults->results[1]->itemSectionRenderer->contents))
            {
                $recomsList = $secondaryResults->results[1]->itemSectionRenderer->contents;
            }
            else if (isset($secondaryResults->results))
            {
                $recomsList = $secondaryResults->results;
            }
            else
            {
                return null;
            }

            InnertubeBrowseConverter::generalLockupConverter($recomsList);

            if (self::shouldUseAutoplay($data))
            {
                if (is_countable($recomsList) && count($recomsList) > 0)
                {
                    $autoplayIndex = self::getRecomAutoplay($recomsList);

                    if (isset($_COOKIE["PREF"]))
                    {
                        $pref = PrefUtils::parse($_COOKIE["PREF"]);
                    }
                    else
                    {
                        $pref = (object) [];
                    }

                    // Move autoplay video to its own object
                    $compactAutoplayRenderer = (object)[
                        "contents" => [ $recomsList[$autoplayIndex] ],
                        "infoText" => $i18n->get("autoplayInfoText"),
                        "title" => $i18n->get("autoplayTitle"),
                        "toggleDesc" => $i18n->get("autoplayToggleDesc"),
                        "checked" => PrefUtils::autoplayEnabled($pref)
                    ];
                    $response += ["compactAutoplayRenderer" => $compactAutoplayRenderer];

                    // Remove the original reference to prevent it from 
                    // rendering twice
                    array_splice($recomsList, $autoplayIndex, 1);
                }
            }

            $response += ["results" => $recomsList];
            return (object)$response;
        }

        return null;
    }

    /**
     * Called from main watch model
     * 
     * This checks if the playlist is present and returns
     * the playlist data if so.
     */
    public static function bakePlaylist(): ?object
    {
        $playlist = &WatchBase::$playlist;
        $i18n = i18n::getNamespace("watch");

        // Return null if there is no playlist, this
        // makes the templater ignore it.
        $out = null;

        if (!is_null($playlist))
        {
            $list = &$playlist->playlist;
            
            $out = $list;

            // Mostly Isabella's messy work
            // TODO: cleanup
            $countText = $list->videoCountText->runs ?? null;
            
            if (!is_null($countText))
            {
                $curIndex = $countText[0]->text;
                $videoCount = $countText[2]->text;

                if ("1" == $videoCount)
                {
                    $videoCount = $i18n->get("playlistVideosSingular");
                }
                else
                {
                    $videoCount = $i18n->format("playlistVideosPlural", $videoCount);
                }

                $out->videoCountText = (object) [
                    "currentIndex" => $curIndex,
                    "videoCount" => $videoCount
                ];
            }

            // "previous/next video ids also need a little work
            //  let's just catch two cases with one"
            // Copied from Isabella's implementation again
            $playlistId = &WatchBase::$yt->playlistId;

            $out->isMix = substr($playlistId, 0, 2) == "RD";

            $curIndexInt = &$list->localCurrentIndex;
            $prevIndexInt = $curIndexInt - 1;
            $nextIndexInt = $curIndexInt + 1;

            if ($prevIndexInt < 0)
            {
                $prevIndexInt = count($list->contents) - 1;
            }

            if ($nextIndexInt > count($list->contents) - 1)
            {
                $nextIndexInt = 0;
            }

            $prevIndexIntPlus = $prevIndexInt + 1;
            $nextIndexIntPlus = $nextIndexInt + 1;

            $prevId = $list->contents[$prevIndexInt]
                ->playlistPanelVideoRenderer->videoId ?? null
            ;
            $prevUrl = "/watch?v={$prevId}&index={$prevIndexIntPlus}&list={$playlistId}";
            $nextId = $list->contents[$nextIndexInt]
                ->playlistPanelVideoRenderer->videoId ?? null
            ;
            $nextUrl = "/watch?v={$nextId}&index={$nextIndexIntPlus}&list={$playlistId}";

            // Previous and next buttons
            // These are hidden, but the JS uses them, and there is also CSS
            // themes that unhide these buttons.
            $out->behaviorControls = [];
            $out->behaviorControls[] = new MButton([
                "size" => "SIZE_DEFAULT",
                "style" => "STYLE_OPACITY",
                "tooltip" => $i18n->get("playlistPrevVideo"),
                "navigationEndpoint" => NavigationEndpoint::createEndpoint($prevUrl),
                "icon" => (object) [
                    "iconType" => "WATCH_APPBAR_PLAY_PREV"
                ],
                "class" => [
                    "hid",
                    "prev-playlist-list-item",
                    "yt-uix-tooltip-masked",
                    "yt-uix-button-player-controls"
                ]
            ]);
            $out->behaviorControls[] = new MButton([
                "size" => "SIZE_DEFAULT",
                "style" => "STYLE_OPACITY",
                "tooltip" => $i18n->get("playlistNextVideo"),
                "navigationEndpoint" => NavigationEndpoint::createEndpoint($nextUrl),
                "icon" => (object) [
                    "iconType" => "WATCH_APPBAR_PLAY_NEXT"
                ],
                "class" => [
                    "hid",
                    "next-playlist-list-item",
                    "yt-uix-tooltip-masked",
                    "yt-uix-button-player-controls"
                ]
            ]);

            $isSaved = $out->menu->menuRenderer->items[0]->toggleMenuServiceItemRenderer->isToggled ?? false;

            if (!$out->isMix)
            {
                // TODO (izzy): Check if this works. I heard reports that this doesn't work.
                $out->saveButton = new MButton([
                    "size" => "SIZE_DEFAULT",
                    "style" => "STYLE_OPACITY",
                    "id" => "gh-playlist-save",
                    "icon" => (object) [],
                    "class" => [
                        "yt-uix-button-player-controls",
                        "yt-uix-playlistlike",
                        "watch-playlist-like",
                        $isSaved ? "yt-uix-button-toggled" : ""
                    ],
                    "tooltip" => $isSaved ? $i18n->get("playlistUnsave") : $i18n->get("playlistSave"),
                    "attributes" => [
                        "like-label" => "",
                        "playlist-id" => $out->playlistId,
                        "unlike-label" => "",
                        "unlike-tooltip" => $i18n->get("playlistUnsave"),
                        "like-tooltip" => $i18n->get("playlistSave"),
                        "toggle-class" => "yt-uix-button-toggled",
                        "token" => "dummy"
                    ]
                ]);
                
                if (!SignIn::isSignedIn())
                {
                    $out->saveButton->clickcard = new MSigninClickcard(
                        $i18n->get("clickcardPlaylistSignIn"),
                        "",
                        [
                            "text" => $i18n->get("clickcardSignIn"),
                            "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
                        ]
                    );
                }
            }
        }

        return $out;
    }

    /**
     * Determine autoplay use status
     * 
     * @return bool
     */
    public static function shouldUseAutoplay(&$data)
    {
        // Disable if watch playlists available at all.
        if (is_null(WatchBase::$playlist))
        {
            return true;
        }

        // If none of the conditions above are hit, always
        // return false as a catch all
        return false;
    }

    /**
     * Get autoplay recommendation
     * 
     * @param object $results (index of the results)
     * @return int The index of the recommendation.
     */
    public static function getRecomAutoplay(&$results)
    {
        for ($i = 0; $i < count($results); $i++) if (isset($results[$i]->compactVideoRenderer))
        {
            return $i;
        }

        return 0;
    }
}