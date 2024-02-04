<?php
namespace Rehike\Model\Watch;

use Rehike\ConfigManager\Config;
use Rehike\Signin\API as SignIn;
use Rehike\Util\PrefUtils;

use Rehike\Model\Watch\AgeGate\MPlayerAgeGate;
use Rehike\Model\Watch\AgeGate\MPlayerContentGate;

use Rehike\Async\Promise;
use function Rehike\Async\async;
use Rehike\i18n\i18n;

/**
 * Implements all logic pertaining to the generation of watch
 * page data.
 * 
 * This is fed into the templater just as a raw response would.
 * Please perform any processing within this scope.
 * 
 * For most implementation, you should look at Watch8\Watch8Subcontroller.php
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class WatchModel
{
    static $useRyd;

    static $yt;
    static $response;
    static $rydData = null;
    static $subController = "";
    static $isLive = false;
    static $isKidsVideo = false;
    static $isOwner = false;

    // Set with the primary info renderer
    static $title;

    // Shorthand data references
    static $results = null;
    static $secondaryResults = null;
    static $playlist = null;
    static $primaryInfo = null;
    static $secondaryInfo = null;
    static $commentSection = null;
    static $liveChat = null;
    static $engagementPanels = null;
    static $frameworkUpdates = null;

    /**
     * Bake a watch page model
     * 
     * This is the insertion point, so other operations are
     * performed within this function as well.
     * 
     * @param object $yt (global state)
     * @param object $data from watch results response
     * @param object $rydData from RYD API response
     * @return object
     */
    public static function bake(&$yt, $data, $videoId, $rydData = null)
    {
        return async(function() use (&$yt, $data, $videoId, $rydData) {
            // Initial logic
            self::$yt = &$yt;
            self::$response = &$data;
            self::$rydData = $rydData;
            self::$useRyd = self::shouldUseRyd();
            self::destructureData($data->contents);
            self::$subController = self::getSubcontroller();
            self::$engagementPanels = $data->engagementPanels ?? null;
            self::$frameworkUpdates = $data->frameworkUpdates ?? null;

            self::$isKidsVideo = self::getIsKidsVideo(self::$secondaryInfo);
            self::$isLive = self::getIsLive(self::$primaryInfo);
            self::$isOwner = self::getIsOwner(self::$secondaryInfo);

            // Get player error
            if ($error = @$yt->playerResponse->playabilityStatus->errorScreen->playerErrorMessageRenderer)
            {
                $status = $yt->playerResponse->playabilityStatus->status ?? "";

                // If it's age restriction, show that
                if ("LOGIN_REQUIRED" == $status)
                {
                    $yt->playerUnavailable = new MPlayerAgeGate();
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
                "isLive" => self::$isLive,
                "isOwner" => self::$isOwner,
                "results" => yield self::bakeResults($data, $videoId),
                "secondaryResults" => self::bakeSecondaryResults($data),
                "title" => self::$title,
                "playlist" => self::bakePlaylist(),
                "liveChat" => self::$liveChat,
                "autonavEnabled" => PrefUtils::autoplayEnabled($pref)
            ];
        });
    }

    /**
     * Get the watch subcontroller for a specific
     * watch variant. By default, this is watch8.
     */
    public static function getSubcontroller()
    {
        return "Rehike\Model\Watch\Watch8\Watch8Subcontroller";
    }

    /**
     * Parse the data provided by the bake function
     * and store global references within here.
     */
    public static function destructureData(&$data)
    {
        self::$results = null;

        // Wrapped in isset to prevent crashes
        if (isset($data->twoColumnWatchNextResults->results->results))
        {
            self::$results = &$data->twoColumnWatchNextResults->results->results;
        }

        self::$secondaryResults = null;
        if (isset($data->twoColumnWatchNextResults->secondaryResults->secondaryResults))
        {
            self::$secondaryResults = &$data->twoColumnWatchNextResults->secondaryResults->secondaryResults;
        }

        if (isset($data->twoColumnWatchNextResults->conversationBar->liveChatRenderer))
        {
            self::$liveChat = &$data->twoColumnWatchNextResults->conversationBar->liveChatRenderer;
        }

        // This one doesn't need to be isset wrapped for some reason
        self::$playlist = &$data->twoColumnWatchNextResults->playlist ?? null;

        // For sub-result references, iteration must be used
        if (isset(self::$results->contents))
        for ($i = 0; $i < count(self::$results->contents); $i++) 
        foreach (self::$results->contents[$i] as $name => &$value)
        switch ($name)
        {
            case "videoPrimaryInfoRenderer":
                self::$primaryInfo = &$value;
                break;
            case "videoSecondaryInfoRenderer":
                self::$secondaryInfo = &$value;
                break;
            case "itemSectionRenderer":
                // Determine based on section ID instead
                if (isset($value->sectionIdentifier))
                switch ($value->sectionIdentifier)
                {
                    case "comment-item-section":
                        self::$commentSection = &$value;
                        break;
                }
                break;
        }
    }

    /**
     * Determine whether or not to use the Return YouTube Dislike
     * API to return dislikes. Retrieved from application config.
     * 
     * @return bool
     */
    public static function shouldUseRyd()
    {
        return null != self::$rydData;
    }

    /**
     * Bake results
     * 
     * Call gets passed to subcontroller for handling.
     * 
     * @param object $data from watch results response
     * @return object
     */
    public static function bakeResults(&$data, &$videoId)
    {
        return async(function() use (&$data, &$videoId) {
            return yield self::$subController::bakeResults($data, $videoId);
        });
    }

    /**
     * Bake secondary results (recommendations)
     * 
     * Call gets passed to subcontroller for handling.
     * 
     * @param object $data from watch results response
     * @return object
     */
    public static function bakeSecondaryResults(&$data)
    {
        return self::$subController::bakeSecondaryResults($data);
    }

    /**
     * Bake playlist
     * 
     * Call gets passed to subcontroller for handling.
     */
    public static function bakePlaylist(): ?object
    {
        return self::$subController::bakePlaylist();
    }

    /**
     * Determine if a video is a kids video or not
     * 
     * @param object $secondaryInfo
     * @return bool
     */
    public static function getIsKidsVideo(&$secondaryInfo)
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
    public static function getIsLive(&$primaryInfo)
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

    public static function getIsOwner(&$secondaryInfo)
    {
        if (!SignIn::isSignedIn()) return false;
        if ($ucid = SignIn::getInfo()["ucid"]) {
            if ($ucid == @$secondaryInfo->owner->videoOwnerRenderer->navigationEndpoint->browseEndpoint->browseId) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}