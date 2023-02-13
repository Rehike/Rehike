<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;

use \Com\Youtube\Innertube\Request\BrowseRequestParams;

use Rehike\Request;
use Rehike\Util\Base64Url;
use Rehike\i18n;
use Rehike\Util\ExtractUtils;
use Rehike\Util\ChannelUtils;

use \Rehike\Model\Channels\Channels4Model as Channels4;

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
        "p"
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
        $this->useJsModule("www/channels");

        // Init i18n
        i18n::newNamespace("channels")->registerFromFolder("i18n/channels");

        // BUG (kirasicecreamm): ChannelUtils::getUcid is hardcoded
        // to look at the path property of the input object.
        // This is bad design.
        $ucid = ChannelUtils::getUcid($request);
        $yt->ucid = $ucid;

        if ($ucid == "") {
            http_response_code(404);
            $this->spfIdListeners = [];
            $this->template = "error/404";
        }

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

        // Perform InnerTube request
        Request::queueInnertubeRequest("main", "browse", (object)[
            "browseId" => $ucid,
            "params" => isset($params) ? Base64Url::encode($params->serializeToString()) : null,
            "query" => $request->params->query ?? null 
        ]);

        if (
            in_array($tab, self::SECONDARY_RESULTS_ENABLED_TAB_IDS) &&
            "featured" != $tab
        )
        {
            Request::queueInnertubeRequest("sidebar", "browse", (object)[
                "browseId" => $ucid
            ]);
        }

        $responses = Request::getResponses();

        $page = json_decode($responses["main"]);

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

                foreach ($tabs as &$tab)
                {
                    if (@$tab->tabRenderer->selected)
                    {
                        $grid = &$tab->tabRenderer->content->richGridRenderer ?? null;
                        break;
                    } 
                }

                if (isset($grid))
                {
                    $ctoken = $grid->header->feedFilterChipBarRenderer->contents[$sort]->chipCloudChipRenderer
                    ->navigationEndpoint->continuationCommand->token ?? null;

                    if (isset($ctoken))
                    {
                        $yt->showSort = true;

                        Request::queueInnertubeRequest("sort", "browse", (object) [
                            "continuation" => $ctoken
                        ]);
                        $newContents = Request::getResponses()["sort"];
                        $newContents = json_decode($newContents);
                        $newContents = $newContents->onResponseReceivedActions[1]->reloadContinuationItemsCommand
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
        if ($request->params->sub_confirmation == "1")
            $yt->subConfirmation = true;

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

        if (isset($responses["sidebar"]))
        {
            $sidebar = json_decode($responses["sidebar"]);
        }
        else if ("featured" == $tab)
        {
            $sidebar = $page;
        }

        $yt->page = Channels4::bake($yt, $page, $sidebar);
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
        Request::queueInnertubeRequest("resolve", "navigation/resolve_url", (object) [
            "url" => "https://www.youtube.com" . $path
        ]);
        $response = Request::getResponses()["resolve"];

        $ytdata = json_decode($response);
        
        if (isset($ytdata->endpoint->watchEndpoint))
        {
            $url = "/watch?v=" . $ytdata->endpoint->watchEndpoint->videoId;
            (require "modules/spfRedirectHandler.php")($url);
        }
    }
}

// Export
return new channel();
