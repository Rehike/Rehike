<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;

use \Com\Youtube\Innertube\Request\BrowseRequestParams;

use Rehike\Network;
use Rehike\Async\Promise;
use YukisCoffee\CoffeeRequest\Network\Response;
use Rehike\Util\Base64Url;
use Rehike\i18n;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ChannelUtils;
use Rehike\Signin\API as SignIn;

use \Rehike\Model\Channels\Channels4Model as Channels4;

use function Rehike\Async\async;

class channel extends NirvanaController {
    public $template = "channel";

    public static $requestedTab = "";

    // Tabs where the "Featured channels" sidebar should show on
    public const SECONDARY_RESULTS_ENABLED_TAB_IDS = [
        "featured",
        "discussion",
        "community",
        "about"
    ];

    // Indices of which cloud chip corresponds to each sort option
    public const VIDEO_TAB_SORT_INDICES = [
        "dd",
        "p",
        "da"
    ];

    // Sort map for regular tabs that still use the old sorting backend
    public const SORT_MAP = [
        null,
        "p",
        "da",
        "dd",
        "lad"
    ];

    public const VIDEO_TABS = [
        "videos",
        "streams"
    ];

    public function onPost(&$yt, $request) {
        http_response_code(404);
        $this->template = "error/404";
    }

    public function onGet(&$yt, $request)
    {
        async(function() use (&$yt, $request) {
            $this->useJsModule("www/channels");

            // Init i18n
            i18n::newNamespace("channels")->registerFromFolder("i18n/channels");

            // BUG (kirasicecreamm): ChannelUtils::getUcid is hardcoded
            // to look at the path property of the input object.
            // This is bad design.
            if ($request->path[0] != "channel")
            {
                $ucid = yield ChannelUtils::getUcid($request);
            }
            else
            {
                $ucid = $request->path[1];
            }

            $yt->ucid = $ucid;

            if ($ucid == "" || is_null($ucid))
            {
                http_response_code(404);
                $this->spfIdListeners = [];
                $this->template = "error/404";
                
                return;
            }

            // If user is signed in and channel owner, get data for the
            // secondary channel header.
            $ownerData = yield ChannelUtils::getOwnerData($ucid);

            // Register the endpoint in the request
            $this->setEndpoint("browse", $ucid);

            // Get the requested tab
            $tab = "featured";
            if (!in_array($request->path[0], ["channel", "user", "c"])) {
                if (isset($request->path[1]) && "" != @$request->path[1]) {
                    $tab = strtolower($request->path[1]);
                }
            } elseif (isset($request->path[2]) && "" != @$request->path[2]) {
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

            // Configure request params
            if ("featured" != $tab ||
                isset($request->params->shelf_id) ||
                isset($request->params->view) ||
                (isset($request->params->sort) && !in_array($tab, ["videos", "streams", "shorts"])))
            {
                $params = new BrowseRequestParams();
                $params->setTab($tab);
            }
            
            if (isset($request->params->shelf_id)) {
                $params->setShelfId((int) $request->params->shelf_id);
            }

            if (isset($request->params->view)) {
                $params->setView((int) $request->params->view);
            }

            if (isset($request->params->sort) && !in_array($tab, ["videos", "streams", "shorts"]))
            {
                $id = array_search($request->params->sort, self::SORT_MAP);
                if (is_int($id))
                {
                    $params->setSort($id);
                }
            }

            // Compose InnerTube requests for later.
            $channelRequest = Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => $ucid,
                    "params" => isset($params)
                        ? Base64Url::encode($params->serializeToString())
                        : null,
                    "query" => $request->params->query ?? null
                ]
            );

            if (
                in_array($tab, self::SECONDARY_RESULTS_ENABLED_TAB_IDS) &&
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
            if (in_array($tab, self::VIDEO_TABS) && isset($request->params->sort))
            {
                // Get index of sort name
                $sort = array_search($request->params->sort, self::VIDEO_TAB_SORT_INDICES);
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

            switch ($request->path[0]) {
                case "c":
                case "user":
                case "channel":
                    $baseUrl = "/" . $request->path[0] . "/" . $request->path[1];
                    break;
                default:
                    $baseUrl = "/" . $request->path[0];
                    break;
            }

            Channels4::registerBaseUrl($baseUrl);
            Channels4::registerCurrentTab($tab);

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

            $yt->page = Channels4::bake(
                yt: $yt,
                data: $page,
                sidebarData: $sidebar,
                ownerData: $ownerData
            );
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
}

// Export
return new channel();
