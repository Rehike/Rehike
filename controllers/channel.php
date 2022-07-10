<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;

use \Com\YouTube\Innertube\Request\BrowseRequestParams;

use Rehike\Request;
use Rehike\Util\Base64Url;
use Rehike\i18n;

require_once "controllers/utils/extractUtils.php";
require_once "controllers/utils/channelUtils.php";

use \ExtractUtils;
use \ChannelUtils;

use \Rehike\Model\Channels\Channels4Model as Channels4;

class ChannelController extends NirvanaController {
    public $template = "channel";

    public static $requestedTab = "";
    
    public const SECONDARY_RESULTS_ENABLED_TAB_IDS = [
        "featured",
        "discussion",
        "community",
        "about"
    ];

    public function onGet(&$yt, $request)
    {
        $this->useJsModule("www/channels");

        // Remove when guide implemented into NirvanaController base.
        include "controllers/mixins/guideNotSpfMixin.php";

        // Init i18n
        $i18n = &i18n::newNamespace("channels");
        $i18n->registerFromFolder("i18n/channels");

        // BUG (kirasicecreamm): ChannelUtils::getUcid is hardcoded
        // to look at the path property of the input object.
        // This is bad design.
        $ucid = ChannelUtils::getUcid($request);
        $yt->ucid = $ucid;

        // Get the requested tab
        $tab = "featured";
        if (isset($request->path[2]) && "" != @$request->path[2])
        {
            $tab = strtolower($request->path[2]);
        }

        self::$requestedTab = $tab;

        // Expose tab to configure frontend JS
        $yt->tab = $tab;

        $baseUrl = "/" . $request->path[0] . "/" . $request->path[1];

        // Configure request params
        $params = new BrowseRequestParams();
        $params->setTab($tab);

        // Perform InnerTube request
        Request::queueInnertubeRequest("main", "browse", (object)[
            "browseId" => $ucid,
            "params" => Base64Url::encode($params->serializeToString())
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

        $yt->response = $responses["main"]; // Maybe remove?

        $page = json_decode($responses["main"]);

        Channels4::registerBaseUrl($baseUrl);

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
}

// Export
return new ChannelController();