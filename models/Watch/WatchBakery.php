<?php
namespace Rehike\Model\Watch;

use Rehike\ConfigManager\Config;
use Rehike\SignInV2\SignIn;
use Rehike\Util\PrefUtils;

use Rehike\Model\Watch\{
    AgeGate\MPlayerAgeGate,
    AgeGate\MPlayerContentGate,
    Watch8\MCreatorBar,
    Watch8\MCreatorBarEditButton,
    Watch8\MVideoDiscussionDelayloadRenderer,
    Watch8\MVideoDiscussionNotice,
    Watch8\MVideoPrimaryInfoRenderer,
    Watch8\MVideoSecondaryInfoRenderer,
    Watch8\PrimaryInfo\MOwner,
};

use Rehike\Model\Comments\CommentsHeader;
use Rehike\Model\Comments\CommentThread;

use Rehike\Model\Browse\InnertubeBrowseConverter;

use Rehike\Model\Common\MButton;

use Rehike\Model\Clickcard\MSigninClickcard;

use Rehike\Model\Traits\NavigationEndpoint;

use Rehike\Async\Promise;
use function Rehike\Async\async;
use Rehike\i18n\i18n;
use Rehike\Model\ViewModelConverter\LockupViewModelConverter;
use Rehike\YtApp;

/**
 * Implements all logic pertaining to the generation of watch
 * page data.
 * 
 * This is fed into the templater just as a raw response would.
 * Please perform any processing within this scope.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class WatchBakery
{
    public bool $useRyd = false;

    public YtApp $yt;
    public object $response;
    public ?object $rydData = null;
    public bool $isLive = false;
    public bool $isKidsVideo = false;
    public bool $isOwner = false;

    // Set with the primary info renderer
    public ?string $title = null;

    // Shorthand data references
    public $results = null;
    public $secondaryResults = null;
    public $autoplay = null;
    public $playlist = null;
    public $primaryInfo = null;
    public $secondaryInfo = null;
    public $commentSection = null;
    public $liveChat = null;
    public $engagementPanels = null;
    public $frameworkUpdates = null;
    
    public ?CollaboratorsParser $collaborators = null;

    /**
     * Bake a watch page model
     * 
     * This is the insertion point, so other operations are
     * performed within this function as well.
     * 
     * @param object $yt (global state)
     * @param object $data from watch results response
     * @param object $rydData from RYD API response
     * 
     * @return Promise<object>
     */
    public function bake(YtApp &$yt, object $data, string $videoId, ?object $rydData = null): Promise/*<object>*/
    {
        return async(function() use (&$yt, $data, $videoId, $rydData) {
            // Initial logic
            $this->yt = &$yt;
            $this->response = &$data;
            $this->rydData = $rydData;
            $this->useRyd = $this->shouldUseRyd();
            $this->destructureData($data->contents);
            $this->engagementPanels = $data->engagementPanels ?? null;
            $this->frameworkUpdates = $data->frameworkUpdates ?? null;

            $this->isKidsVideo = $this->getIsKidsVideo($this->secondaryInfo);
            $this->isLive = $this->getIsLive($this->primaryInfo);
            $this->isOwner = $this->getIsOwner($this->secondaryInfo);
            
            if ($this->getHasMultipleOwners())
            {
                $this->collaborators = new CollaboratorsParser($this);
            }

            // Get player error
            if ($error = @$yt->playerResponse->playabilityStatus->errorScreen->playerErrorMessageRenderer)
            {
                $status = $yt->playerResponse->playabilityStatus->status ?? "";

                // If it's age restriction, show that
                if ("LOGIN_REQUIRED" == $status)
                {
                    $yt->playerUnavailable = new MPlayerAgeGate($error);
                }
                else if ("AGE_CHECK_REQUIRED" == $status)
                {
                    $yt->playerUnavailable = new MPlayerAgeGate($error);
                }
                else if ("CONTENT_CHECK_REQUIRED" == $status)
                {
                    /*
                    * Content that requires a content check (i.e. suicide-related)
                    * does not require the user to be signed in.
                    */
                    $yt->playerUnavailable = new MPlayerContentGate($error);
                }
                else
                {
                    $yt->playerUnavailable = $error;

                    if (!isset($yt->playerUnavailable->subreason))
                    {
                        $yt->playerUnavailable->subreason = (object)[
                            "simpleText" => i18n::getRawString(
                                "global", "sorryAboutThat"
                            )
                        ];
                    }
                }
            }

            if (isset($_COOKIE["PREF"]))
            {
                $pref = PrefUtils::parse($_COOKIE["PREF"]);
            }
            else
            {
                $pref = (object) [];
            }

            // Model baking logic
            return (object) [
                "isLive" => $this->isLive,
                "isOwner" => $this->isOwner,
                "results" => yield $this->bakeResults($data, $videoId),
                "secondaryResults" => yield $this->bakeSecondaryResults($data),
                "title" => $this->title,
                "playlist" => $this->bakePlaylist(),
                "liveChat" => $this->liveChat,
                "autonavEnabled" => PrefUtils::autoplayEnabled($pref)
            ];
        });
    }

    /**
     * Parse the data provided by the bake function
     * and store global references within here.
     */
    public function destructureData(object &$data): void
    {
        $this->results = null;

        // Wrapped in isset to prevent crashes
        if (isset($data->twoColumnWatchNextResults->results->results))
        {
            $this->results = &$data->twoColumnWatchNextResults->results->results;
        }

        $this->secondaryResults = null;
        if (isset($data->twoColumnWatchNextResults->secondaryResults->secondaryResults))
        {
            $this->secondaryResults = &$data->twoColumnWatchNextResults->secondaryResults->secondaryResults;
        }

        $this->autoplay = null;
        if (isset($data->twoColumnWatchNextResults->autoplay->autoplay))
        {
            $this->autoplay = &$data->twoColumnWatchNextResults->autoplay->autoplay;
        }

        if (isset($data->twoColumnWatchNextResults->conversationBar->liveChatRenderer))
        {
            $this->liveChat = &$data->twoColumnWatchNextResults->conversationBar->liveChatRenderer;
        }

        // This one doesn't need to be isset wrapped for some reason
        $this->playlist = &$data->twoColumnWatchNextResults->playlist ?? null;

        // For sub-result references, iteration must be used
        if (isset($this->results->contents))
        for ($i = 0; $i < count($this->results->contents); $i++) 
        foreach ($this->results->contents[$i] as $name => &$value)
        switch ($name)
        {
            case "videoPrimaryInfoRenderer":
                $this->primaryInfo = &$value;
                break;
            case "videoSecondaryInfoRenderer":
                $this->secondaryInfo = &$value;
                break;
            case "itemSectionRenderer":
                // Determine based on section ID instead
                if (isset($value->sectionIdentifier))
                switch ($value->sectionIdentifier)
                {
                    case "comment-item-section":
                        $this->commentSection = &$value;
                        break;
                }
                break;
        }
    }

    /**
     * Determine whether or not to use the Return YouTube Dislike
     * API to return dislikes. Retrieved from application config.
     */
    public function shouldUseRyd(): bool
    {
        return null != $this->rydData;
    }

    /**
     * Bake results
     * 
     * @param object $data from watch results response
     * @return ?object
     */
    public function bakeResults(object &$data, string &$videoId): ?object
    {
        return async(function () use (&$data, $videoId)
        {
            // Create references
            $primaryInfo = $this->primaryInfo;
            $secondaryInfo = $this->secondaryInfo;
            $commentSection = $this->commentSection;

            $results = [];

            // Push creator bar if the video is yours
            if ($this->isOwner)
            {
                $results[] = (object) [
                    "videoCreatorBarRenderer" => new MCreatorBar($videoId)
                ];
            }

            // Push primary info (if it exists)
            if (!is_null($primaryInfo)) $results[] = (object)[
                "videoPrimaryInfoRenderer" => new MVideoPrimaryInfoRenderer($this, $videoId)
            ];

            // Push secondary info (if it exists)
            if (!is_null($secondaryInfo)) $results[] = (object)[
                "videoSecondaryInfoRenderer" => new MVideoSecondaryInfoRenderer($this)
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
                    $this->yt->commentsToken = $continuationToken;

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
                    $videoId = $this->yt->videoId ?? $_GET["v"];

                    $headerRenderer = CommentsHeader::fromData(
                        $commentSection?->header[0]?->commentsHeaderRenderer
                    );

                    $commentsBakery = new CommentThread($this->response);
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
     * Bake secondary results (recommendations)
     * 
     * This performs check for autoplay and moves the video
     * to its respective position if so.
     * 
     * @param object $data from watch results response
     * @return Promise<?object>
     */
    public function bakeSecondaryResults(object &$data): Promise/*<?object>*/
    {
        return async(function() use ($data) {
        // Get data from the reference in the datahost
        $origResults = &$this->secondaryResults;
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

            InnertubeBrowseConverter::generalLockupConverter(
                $recomsList,
                [ "lockupStyle" => LockupViewModelConverter::STYLE_COMPACT ]
            );
            
            $targetObjs = [];
            $videoIds = [];
            $fullViewCountsMgr = new FullViewCountsManager();
            
            foreach ($recomsList as $recom)
            {
                if (isset($recom->compactVideoRenderer))
                {
                    $videoId = $recom->compactVideoRenderer->videoId;
                    
                    $videoIds[] = $videoId;
                    $targetObjs[$videoId] = $recom->compactVideoRenderer;
                }
            }
            
            /** @var FullViewCountsStrategy */
            $fullViewCountsResult = yield $fullViewCountsMgr->requestFullViewCountsForSet($videoIds);
            
            if ($fullViewCountsResult->isSuccessful())
            {
                foreach ($fullViewCountsResult->map as $videoId => $viewCount)
                {
                    // Members-only videos have inaccessible view counts to non-members. In such cases, no view
                    // count is displayed at all.
                    // XXX(isabella): This currently displays the publish time even when the setting is disabled
                    // in Rehike. This is because of a limitation in VideoRendererViewModelConverter which
                    // always assumes that the first metadata row is the video view count, and the second metadata
                    // row is the video publication date. In the case of such videos, the entire view count row
                    // is missing, so the publish time IS the view count and there's really no proper publish time
                    // attribute.
                    if ($viewCount->format == FullViewCountsViewCountFormat::BadResult)
                    {
                        continue;
                    }
                    
                    $targetObjs[$videoId]->viewCountText = $this->getFormattedFullViewCountText($viewCount);
                }
            }

            if ($this->shouldUseAutoplay())
            {
                if (is_countable($recomsList) && count($recomsList) > 0)
                {
                    $autoplayIndex = $this->getRecomAutoplay($recomsList);
                    if ($autoplayIndex != -1)
                    {
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
                        array_splice($recomsList, $autoplayIndex, 1); // ignore IDE type error
                    }
                }
            }

            $response += ["results" => $recomsList];
            return (object)$response;
        }

        return null;
        });
    }

    /**
     * Bake playlist
     * 
     * This checks if the playlist is present and returns
     * the playlist data if so.
     */
    public function bakePlaylist(): ?object
    {
        $playlist = &$this->playlist;
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
            $playlistId = &$this->yt->playlistId;

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
    public function shouldUseAutoplay(): bool
    {
        return !is_null($this->autoplay);
    }

    /**
     * Get autoplay recommendation
     * 
     * @param object $results (index of the results)
     * @return int The index of the recommendation, or -1 if no autoplay video.
     */
    public function getRecomAutoplay(array $results)
    {
        if (!is_null($this->autoplay))
        {
            $videoId = $this->autoplay->sets[0]->autoplayVideo->watchEndpoint->videoId;
            foreach ($results as $i => $result)
            {
                if (@$result->compactVideoRenderer->videoId == $videoId)
                    return $i;
            }
        }
        return -1;
    }

    /**
     * Determine if a video is a kids video or not
     * 
     * @param object $secondaryInfo
     * @return bool
     */
    public function getIsKidsVideo(&$secondaryInfo)
    {
        if (!isset($secondaryInfo->metadataRowContainer->rows)) return false;

        if ($rows = $secondaryInfo->metadataRowContainer)
        {
            foreach ($rows->metadataRowContainerRenderer->rows as $item)
            foreach ($item as $name => $contents)
            if ("richMetadataRowRenderer" == $name &&
                "https://www.youtubekids.com" == $contents->contents[0]->richMetadataRenderer->endpoint->urlEndpoint->url
            )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a video is live or not
     * 
     * @param object $primaryInfo
     * @return bool
     */
    public function getIsLive(&$primaryInfo)
    {
        if (true == @$primaryInfo->viewCount->videoViewCountRenderer->isLive)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getIsOwner(&$secondaryInfo)
    {
        if (!SignIn::isSignedIn()) return false;
        
        if ($ucid = SignIn::getSessionInfo()->getUcid())
        {
            if ($ucid == @$secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function getHasMultipleOwners()
    {
        return isset($this->secondaryInfo->owner->videoOwnerRenderer->avatarStack);
    }
    
    private function getFormattedFullViewCountText(FullViewCountsViewCount $fullViewCount): string
    {
        return match ($fullViewCount->format)
        {
            FullViewCountsViewCountFormat::RawNumber => self::formatViewCountTextWithNumber((int)$fullViewCount->viewCount),
            
            FullViewCountsViewCountFormat::BadResult => 
                trigger_error("BadResult should not make it into " . __METHOD__, E_USER_WARNING),
            
            // The data is already formatted by InnerTube, so we'll just return it raw.
            FullViewCountsViewCountFormat::FormattedByInnertube => $fullViewCount->viewCount,
            default => $fullViewCount->viewCount,
        };
    }
    
    private static function formatViewCountTextWithNumber(int $number): string
    {
        $i18n = i18n::getNamespace("misc");
        
        if ($number == 1)
        {
            return $i18n->format("viewTextSingular", "1");
        }
        else
        {
            return $i18n->format("viewTextPlural", $i18n->formatNumber($number));
        }
    }
}