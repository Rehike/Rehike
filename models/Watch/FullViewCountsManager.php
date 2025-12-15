<?php
namespace Rehike\Model\Watch;

use Rehike\Async\Promise;
use Rehike\Network;
use Rehike\Network\IResponse;
use Rehike\SignInV2\SignIn;
use Rehike\Util\ParsingUtils;

use function Rehike\Async\async;

/**
 * @enum
 */
final class FullViewCountsViewCountFormat
{
    /**
     * The result we got is a raw number. In this case, we need to manually format it ourselves.
     */
    public const RawNumber = 0;
    
    /**
     * This view count is already formatted by InnerTube, so it's already in natural language.
     * In this case, we don't need to do anything at all.
     */
    public const FormattedByInnertube = 1;
    
    /**
     * This particular item has a bad result. Maybe the went private in between the time we got
     * the main watch next result and requested for full view counts?
     * 
     * In this case, we simply ignore the bad item.
     */
    public const BadResult = 2;
}

class FullViewCountsViewCount
{
    /**
     * The format of this view count information.
     * 
     * This is used by the bakery to determine if we can just plop the string into the data
     * by itself, or if it needs to be manually formatted into a particular language string
     * by Rehike.
     * 
     * @var FullViewCountsViewCountFormat
     */
    public int $format = FullViewCountsViewCountFormat::BadResult;
    
    public string $viewCount = "0";
}

/**
 * @enum
 */
final class FullViewCountsStrategyStatus
{
    public const Succeeded = 0;
    public const Failed = 1;
}

class FullViewCountsStrategy
{
    /**
     * @var FullViewCountsStrategyStatus
     */
    public int $status = FullViewCountsStrategyStatus::Failed;
    
    public string $name = "";
    
    /**
     * Map of video IDs to a {@see FullViewCountViewCount} object.
     * 
     * @var FullViewCountsViewCount[]
     */
    public array $map = [];
    
    public function isSuccessful(): bool
    {
        return $this->status == FullViewCountsStrategyStatus::Succeeded;
    }
    
    public function __construct(string $name = "unnamed")
    {
        $this->name = $name;
    }
}

/**
 * Manages retrieval of full view counts for recommendations on the watch page.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class FullViewCountsManager
{
    /**
     * Requests the full view count for a set of video IDs. This function internally uses a set
     * of different strategies to quickly and efficiently retrieve the video IDs so that we can
     * get a response to the user as fast as possible.
     * 
     * @param string[] $videoIds Set of video IDs to request the full view count for.
     * 
     * @return Promise<FullViewCountsStrategy>
     */
    public function requestFullViewCountsForSet(array $videoIds): Promise/*<FullViewCountsStrategy>*/
    {
        return async(function() use ($videoIds)
        {
            // Get empty metadata for the object before we try any strategies.
            $lastStrategyResult = new FullViewCountsStrategy();
            
            $strategies = [
                "requestViaCreator",
                
                // This should typically be the final strategy we try, as it is the
                // slowest and most intensive one.
                "requestViaReel",
            ];
            
            foreach ($strategies as $strategy)
            {
                $lastStrategyResult = yield $this->{$strategy}($videoIds);
                
                if ($lastStrategyResult->status == FullViewCountsStrategyStatus::Succeeded)
                {
                    break;
                }
            }
            
            return $lastStrategyResult;
        });
    }
    
    /**
     * Requests full view counts via the reel_item_watch API. This is the slowest method and
     * should be considered the ultimate fallback if no other strategies are available.
     * 
     * reel_item_watch remains the fastest general InnerTube API (that is, it can be accessed
     * while logged out) to obtain metadata about a video, however, it still has a significant
     * performance penalty to make so many requests.
     * 
     * In order to improve performance, this implementation fires all requests simultaneously
     * so that we can get each one as soon as possible.
     * 
     * @return Promise<FullViewCountsStrategy>
     */
    private function requestViaReel(array $videoIds): Promise/*<FullViewCountsStrategy>*/
    {
        return async(function() use ($videoIds)
        {
            $requests = [];
            $fullCounts = [];
            
            // Before we do anything, we're going to prepare an array with all
            // view count result objects. None of these will have data populated.
            // At the same time, we're already iterating through the list to make
            // the requests, so we'll do both at the same time.
            foreach ($videoIds as $videoId)
            {
                // This will initialise as a BadResult, which means if the data isn't
                // filled in in the future, then it will be treated properly as
                // uninitialised.
                $fullCounts[$videoId] = new FullViewCountsViewCount();
                
                $requests[$videoId] = Network::innertubeRequest(
                    "reel/reel_item_watch",
                    [
                        "disablePlayerResponse" => true,
                        "playerRequest" => (object)[
                            "videoId" => $videoId,
                        ],
                    ]
                );
            }
            
            $responses = yield Promise::all($requests);
            
            $anySuccess = false;
            
            foreach ($responses as $videoId => $response)
            {
                $data = $response->getJson();
                
                if (isset($data->engagementPanels))
                {
                    foreach ($data->engagementPanels as $panelWrapper)
                    {
                        if (
                            isset($panelWrapper->engagementPanelSectionListRenderer) && 
                            $panelWrapper->engagementPanelSectionListRenderer->targetId 
                                == "engagement-panel-structured-description"
                        )
                        {
                            // This is the description renderer, which houses the view count.
                            // The path from now should be straightforward enough.
                            $viewCountObj = $panelWrapper->engagementPanelSectionListRenderer->content
                                ->structuredDescriptionContentRenderer->items[0]->videoDescriptionHeaderRenderer->views;
                                
                            $viewCountStr = ParsingUtils::getText($viewCountObj);
                            
                            $fullCounts[$videoId]->format = FullViewCountsViewCountFormat::FormattedByInnertube;
                            $fullCounts[$videoId]->viewCount = $viewCountStr;
                            $anySuccess = true;
                        }
                    }
                }
            }
            
            
            $strat = new FullViewCountsStrategy(__FUNCTION__);
            
            if (!$anySuccess)
            {
                // Return the default object with no metadata and failed state. We only
                // do this if none of the requests succeeded, so that we can move on to
                // the next strategy (although this particular strategy is considered at
                // the time of design to be the ultimate strategy)
                return $strat;
            }
            
            $strat->status = FullViewCountsStrategyStatus::Succeeded;
            $strat->map = $fullCounts;
            return $strat;
        });
    }
    
    /**
     * Requests full view counts via the YouTube Studio API. This is a fast method as all video
     * information requests can be coalesced into a single remote request, however it requires
     * the user to be logged in.
     * 
     * @return Promise<FullViewCountsStrategy>
     */
    private function requestViaCreator(array $videoIds): Promise/*<FullViewCountsStrategy>*/
    {
        return async(function() use ($videoIds)
        {
            $result = new FullViewCountsStrategy(__FUNCTION__);
            
            if (!SignIn::isSignedIn())
            {
                // Return the default object and move on to the next strategy if the user is not
                // logged in, as the creator API is inaccessible.
                return $result;
            }
            
            $fullCounts = [];
            
            // Prepopulate the list with empty values:
            foreach ($videoIds as $videoId)
            {
                $fullCounts[$videoId] = new FullViewCountsViewCount();
            }
            
            /** @var IResponse */
            $response = yield Network::innertubeRequest(
                "creator/get_creator_videos",
                [
                    "mask" => (object)[
                        "metrics" => (object)[
                            "all" => "true"
                        ]
                    ],
                    "permissions" => (object)[
                        "overallPermissions" => true,
                    ],
                    "videoIds" => $videoIds,
                ],
                "WEB_CREATOR",
                "1.20220207.02.00",
                ignoreErrors: true,
            );
            
            $data = $response->getJson();
            
            if (isset($data->videos))
            {
                foreach ($data->videos as $video)
                {
                    if (isset($video->videoId) && isset($video->metrics->viewCount))
                    {
                        $fullCounts[$video->videoId]->format = FullViewCountsViewCountFormat::RawNumber;
                        $fullCounts[$video->videoId]->viewCount = $video->metrics->viewCount;
                    }
                }
            }
            else
            {
                // We didn't get a valid result, so move on.
                return $result;
            }
            
            $result->status = FullViewCountsStrategyStatus::Succeeded;
            $result->map = $fullCounts;
            return $result;
        });
    }
}