<?php
namespace Rehike\Controller;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
    IController,
};

use Com\Google\Protos\Youtube\Api\Innertube\BrowseContinuation;
use Com\Google\Protos\Youtube\Api\Innertube\BrowseContinuationWrapper;
use Com\Google\Protos\Youtube\Api\Innertube\ContinuationWrapper;
use Rehike\Controller\core\NirvanaController;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Com\Youtube\Innertube\Request\BrowseRequestParams;
use Com\Youtube\Innertube\Request\ModernBrowseAboutParams;
use Com\Youtube\Innertube\Request\ModernBrowseChannelsParams;
use Com\Youtube\Innertube\Request\ModernBrowseCommunityParams;
use Com\Youtube\Innertube\Request\ModernBrowseCoursesParams;
use Com\Youtube\Innertube\Request\ModernBrowseFeaturedParams;
use Com\Youtube\Innertube\Request\ModernBrowseLiveParams;
use Com\Youtube\Innertube\Request\ModernBrowsePlaylistParams;
use Com\Youtube\Innertube\Request\ModernBrowsePodcastsParams;
use Com\Youtube\Innertube\Request\ModernBrowseReleasesParams;
use Com\Youtube\Innertube\Request\ModernBrowseSearchParams;
use Com\Youtube\Innertube\Request\ModernBrowseShoppingParams;
use Com\Youtube\Innertube\Request\ModernBrowseShortsParams;
use Com\Youtube\Innertube\Request\ModernBrowseVideoParams;
use Com\Youtube\Innertube\Request\ModernBrowseVideoParams\ModernSort;
use Com\Youtube\Innertube\Request\ModernMetadata;
use Com\Youtube\Innertube\Request\ModernWebParams;
use Rehike\Network;
use Rehike\Async\Promise;
use Rehike\Exception\Network\InnertubeFailedRequestException;
use Rehike\Util\Base64Url;
use Rehike\Util\ExtractUtils;
use Rehike\Helper\ChannelUtils;
use Rehike\SignInV2\SignIn;

use \Rehike\Model\Channels\Channels4Model as Channels4;

use function Rehike\Async\async;

/**
 * Channel page controller.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ChannelPageController extends NirvanaController implements IGetController, IPostController
{
    public string $template = "channel";

    public static string $requestedTab = "";

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        http_response_code(404);
        $this->template = "error/404";
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        async(function() use (&$yt, $request) {
            $this->useJsModule("www/channels");

            if ($request->path[0] != "channel")
            {
                $ucid = yield ChannelUtils::getUcid(
                    implode("/", $request->path)
                );
            }
            else
            {
                $ucid = $request->path[1];
            }

            $yt->ucid = $ucid;

            if ($ucid == "" || is_null($ucid))
            {
                http_response_code(404);
                $this->template = "error/404";
                
                return;
            }

            // If user is signed in and channel owner, get data for the
            // secondary channel header.
            $ownerData = null;
            if ($ucid == SignIn::getSessionInfo()->getUcid())
            {
                $ownerData = yield ChannelUtils::getOwnerData($ucid);
            }

            // Register the endpoint in the request
            $this->setEndpoint("browse", $ucid);

            // Get the requested tab
            $tab = "featured";
            if (!in_array($request->path[0], ["channel", "user", "c"]))
            {
                if (isset($request->path[1]) && "" != @$request->path[1])
                {
                    $tab = strtolower($request->path[1]);
                }
            }
            else if (isset($request->path[2]) && "" != @$request->path[2])
            {
                $tab = strtolower($request->path[2]);
            }

            self::$requestedTab = $tab;

            // Handle live tab redirect (if the channel is livestreaming)
            if ("live" == $tab)
            {
                $this->handleLiveTabRedirect($request->rawPath);
            }

            // Expose tab to configure frontend JS
            $yt->tab = $tab;
            
            $params = new BrowseRequestParams();
            $useParams = false;
            
            // 2025-07 (or so): Browse requests now require additional protobuf metadata to work.
            $modernMetadata = new ModernMetadata();
            $modernParams = new ModernWebParams();
            switch ($tab)
            {
                case "videos":
                {
                    // Videos tab is a unique case where sort information must be specified.
                    // The default sort order is newest first.
                    $modernVideoParams = new ModernBrowseVideoParams();
                    
                    // 2025-10: This is now counted as an invalid parameter. Whatever man.
                    // I don't even care.
                    //$modernVideoParams->setSort(ModernSort::LATEST);
                    
                    $modernParams->setVideosParams($modernVideoParams);
                    break;
                }
                
                default:
                {
                    match ($tab)
                    {
                        "featured" => $modernParams->setFeaturedParams(new ModernBrowseFeaturedParams()), 
                        "about" => $modernParams->setAboutParams(new ModernBrowseAboutParams()),
                        "shopping" => $modernParams->setShoppingParams(new ModernBrowseShoppingParams()),
                        "playlists" => $modernParams->setPlaylistParams(new ModernBrowsePlaylistParams()),
                        "community", "posts" => $modernParams->setCommunityParams(new ModernBrowseCommunityParams()),
                        "channels" => $modernParams->setChannelsParams(new ModernBrowseChannelsParams()),
                        "search" => $modernParams->setSearchParams(new ModernBrowseSearchParams()),
                        "live" => $modernParams->setLiveParams(new ModernBrowseLiveParams()),
                        "shorts" => $modernParams->setShortsParams(new ModernBrowseShortsParams()),
                        "releases" => $modernParams->setReleasesParams(new ModernBrowseReleasesParams()),
                        "podcasts" => $modernParams->setPodcastsParams(new ModernBrowsePodcastsParams()),
                        "courses" => $modernParams->setCoursesParams(new ModernBrowseCoursesParams()),
                        
                        // If this is an unspecified tab, do nothing.
                        default => null,
                    };
                }
            }
            $modernMetadata->setWebParams($modernParams);

            // Configure request params
            if ("featured" != $tab ||
                isset($request->params->shelf_id) ||
                isset($request->params->view) ||
                (isset($request->params->sort) && !in_array($tab, ["videos", "streams", "shorts"])))
            {
                $useParams = true;
                $params->setTab($tab);
                $params->setModernMetadata($modernMetadata);
            }
            
            if (isset($request->params->shelf_id))
            {
                $params->setShelfId((int) $request->params->shelf_id);
            }

            if (isset($request->params->view))
            {
                $params->setView((int) $request->params->view);
            }

            if (isset($request->params->sort) && !in_array($tab, ["videos", "streams", "shorts"]))
            {
                $id = array_search($request->params->sort, Channels4::SORT_MAP);
                if (is_int($id))
                {
                    $params->setSort($id);
                }
            }

            // Compose InnerTube requests for later.
            if ($tab == "about")
            {
                $channelRequest = $this->requestAbout($request, $ucid, Base64Url::encode($params->serializeToString()));
            }
            else
            {
                $channelRequest = Network::innertubeRequest(
                    action: "browse",
                    body: [
                        "browseId" => $ucid,
                        "params" => $useParams
                            ? Base64Url::encode($params->serializeToString())
                            : null,
                        "query" => $request->params->query ?? null
                    ]
                );
            }

            if (
                in_array($tab, Channels4::SECONDARY_RESULTS_ENABLED_TAB_IDS) &&
                "featured" != $tab
            )
            {
                $sidebarRequest = Network::innertubeRequest(
                    action: "browse",
                    body: [
                        "browseId" => $ucid
                    ]
                );
            }
            else
            {
                $sidebarRequest = new Promise(fn($r) => $r());
            }

            // Run the channel and sidebar requests at the same time and store them in different
            // variables.
            [$channelResponse, $sidebarResponse] = yield Promise::all($channelRequest, $sidebarRequest);

            $page = $channelResponse->getJson();

            $yt->response = $page;

            // Get content for current sort if it
            // is not recently uploaded (default)
            $yt->videosSort = 0;
            if (in_array($tab, Channels4::VIDEO_TABS) && isset($request->params->sort))
            {
                // Get index of sort name
                $sort = array_search($request->params->sort, Channels4::VIDEO_TAB_SORT_INDEX_MAP);
                $yt->videosSort = $sort;
                if ($sort > 0)
                {
                    $tabs = &$page->contents->twoColumnBrowseResultsRenderer->tabs;

                    // Do NOT call this $tab. It will override the previous $tab
                    // and cause an object to be registered as the current tab.
                    foreach ($tabs as &$tabR)
                    {
                        if (@$tabR->tabRenderer->selected)
                        {
                            $grid = &$tabR->tabRenderer->content->richGridRenderer ?? null;
                            break;
                        } 
                    }

                    if (isset($grid))
                    {
                        $ctoken = $grid->header->feedFilterChipBarRenderer->contents[$sort]
                            ->chipCloudChipRenderer->navigationEndpoint->continuationCommand
                            ->token ?? null;

                        if (isset($ctoken))
                        {
                            $sort = yield Network::innertubeRequest(
                                action: "browse",
                                body: [
                                    "continuation" => $ctoken
                                ]
                            );

                            $newContents = $sort->getJson();
                            $newContents = $newContents
                                ->onResponseReceivedActions[1]
                                ->reloadContinuationItemsCommand
                                ->continuationItems ?? null;

                            if (isset($newContents) && is_array($newContents))
                            {
                                $grid->contents = $newContents;
                            }
                        }
                    }
                }
            }

            $yt->subConfirmation = false;

            if (isset($request->params->sub_confirmation))
            {
                if ($request->params->sub_confirmation == "1")
                {
                    $yt->subConfirmation = true;
                }
            }

            switch ($request->path[0])
            {
                case "c":
                case "user":
                case "channel":
                    $baseUrl = "/" . $request->path[0] . "/" . $request->path[1];
                    break;
                default:
                    $baseUrl = "/" . $request->path[0];
                    break;
            }
            
            $c4Bakery = new Channels4();

            $c4Bakery->registerBaseUrl($baseUrl);
            $c4Bakery->registerCurrentTab($tab);

            // Handle the sidebar
            $sidebar = null;

            if (isset($sidebarResponse))
            {
                $sidebar = $sidebarResponse->getJson();
            }
            else if ("featured" == $tab)
            {
                $sidebar = $page;
            }

            $yt->page = $c4Bakery->bake(
                yt: $yt,
                data: $page,
                sidebarData: $sidebar,
                ownerData: $ownerData
            );

            if (isset($yt->page->title))
            {
                $this->setTitle($yt->page->title);
            }
        });
    }

    /**
     * Redirect to a channel's livestream by visiting their live URL.
     * 
     * This only works if said channel is in the process of livestreaming,
     * otherwise this will have no effect and will simply take you to the
     * featured tab of the channel.
     */
    public function handleLiveTabRedirect($path)
    {
        Network::innertubeRequest(
            action: "navigation/resolve_url",
            body: [
                "url" => "https://www.youtube.com" . $path
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();
        
            if (isset($ytdata->endpoint->watchEndpoint))
            {
                $url = "/watch?v=" . $ytdata->endpoint->watchEndpoint->videoId;
                (require "includes/spf_redirect_handler.php")($url);
            }
        });
    }

    /**
     * 2023/11/13: Channel about requests are pretty strange.
     *
     * Currently, we rely on the standard browse continuation method that Polymer
     * uses.
     *
     * The channel header must be zippered together from another response, so we
     * must request twice.
     */
    private function requestAbout(RequestMetadata $request, string $ucid, string $browseParam): Promise
    {
        return async(function() use ($request, $ucid, $browseParam) {
            $headerRequest = Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => $ucid
                ]
            );
            
            // Build a browse continuation for the about request:
            $paramsBuilderInner = new BrowseContinuationWrapper([
                "browse_id" => $ucid,
                
                // We don't have a reversed protobuf of this yet, so here's the general structure:
                //
                // {
                //     110: {
                //         3: {
                //             19: {
                //                 1: "66b9c56e-0000-2aff-91fb-883d24fc5398"
                //             }
                //         }
                //     }
                // }
                //
                // Note that the GUID here has no significant meaning in Rehike. It is just an
                // element target ID in Polymer.
                "encoded_action" => "8gYrGimaASYKJDY2YjljNTZlLTAwMDAtMmFmZi05MWZiLTg4M2QyNGZjNTM5OA%3D%3D"
            ]);
            $paramsBuilderOuter = new ContinuationWrapper([
                "browse_continuation" => $paramsBuilderInner
            ]);
            $params = Base64Url::encode($paramsBuilderOuter->serializeToString());

            $aboutRequest = Network::innertubeRequest(
                action: "browse",
                body: [
                    "continuation" => $params
                ]
            );

            $responses = yield Promise::all([
                "header" => $headerRequest,
                "about"  => $aboutRequest
            ]);

            $baseResponse = $responses["header"]->getJson();
            $aboutResponse = $responses["about"]->getJson();

            if (!\Rehike\Debugger\Debugger::isCondensed())
            {
                // In the case of a non-condensed debugger, we will add this debug property
                // to the global object so that we can see its contents in the debugger.
                $this->yt->testAboutResponse = $aboutResponse;
            }

            // We add this property to the base response so that we can access it when parsing
            // channel contents.
            $baseResponse->rehikeAboutTab = @$aboutResponse
                ->onResponseReceivedEndpoints[0]->appendContinuationItemsAction
                ->continuationItems[0]->aboutChannelRenderer->metadata
                ->aboutChannelViewModel;

            // Hack to generate a new, modified network response since we're
            // expecting it earlier in the code.
            return new \Rehike\Network\Internal\Response(
                $responses["header"]->sourceRequest, 
                200, 
                json_encode($baseResponse),
                (array)$responses["header"]->headers
            );
        });
    }
}