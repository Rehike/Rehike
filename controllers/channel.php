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

    public function onPost(&$yt, $request) {
        http_response_code(404);
        $this->template = "error/404";
    }

    public function onGet(&$yt, $request)
    {
        $this->useJsModule("www/channels");

        // Init i18n
        $i18n = &i18n::newNamespace("channels");
        $i18n->registerFromFolder("i18n/channels");

        // BUG (kirasicecreamm): ChannelUtils::getUcid is hardcoded
        // to look at the path property of the input object.
        // This is bad design.
        ChannelUtils::getUcid($request)->then(function ($ucid) 
            use ($yt, $request) 
        {
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
            $params = new BrowseRequestParams();
            $params->setTab($tab);

            if (isset($request->params->shelf_id)) {
                $params->setShelfId((int) $request->params->shelf_id);
            }

            if (isset($request->params->view)) {
                $params->setView((int) $request->params->view);
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

            // Perform InnerTube request
            $channelRequest = Network::innertubeRequest(
                action: "browse",
                body: [
                    "browseId" => $ucid,
                    "params" => Base64Url::encode($params->serializeToString()),
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

            Promise::all([
                "channel" => $channelRequest,
                "sidebar" => $sidebarRequest
            ])->then(function (array $responses) use ($yt) {
                $channel = $responses["channel"]->getJson();
                
                if ($responses["sidebar"] instanceof Response)
                {
                    $sidebar = $responses["sidebar"]->getJson();
                }
                else
                {
                    $sidebar = $channel;
                }

                $yt->page = Channels4::bake($yt, $channel, $sidebar);
            });
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
