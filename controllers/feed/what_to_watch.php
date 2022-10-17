<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Request;
use Rehike\RehikeConfigManager as Config;
use Rehike\Util\AndroidW2w15Parser;
use Rehike\Util\WebV2Shelves;
use Rehike\Util\RichShelfUtils;

/**
 * What to Watch (home) feed controller
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @version 1.0.20220805
 */
class FeedWhatToWatchController extends NirvanaController {
    const BROWSE_ID              = 'FEwhat_to_watch';
    const STYLE_SHELVES_TEMPLATE = 'feed/what_to_watch';
    const STYLE_GRIDDED_TEMPLATE = 'feed/what_to_watch_grid';

    public $template;

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint('browse', self::BROWSE_ID);
        $yt->enableFooterCopyright = true;

        // get style
        if (Config::getConfigProp('useGridHomeStyle' ?? false))
        {
            $this->template = self::STYLE_GRIDDED_TEMPLATE;

            self::buildStyleGridded($yt);
        }
        else
        {
            $this->template = self::STYLE_SHELVES_TEMPLATE;

            $yt->page = self::buildStyleShelves();
        }
    }

    /**
     * Build the categorised "shelves" home page style
     * 
     * @return void
     */
    private static function buildStyleShelves() {
        // Initial Android request to get continuation
        Request::queueInnertubeRequest(
            "android",
            "browse", 
            (object)[
                "browseId" => self::BROWSE_ID
            ],
            "ANDROID",
            "17.14.33"
        );
        $android = Request::getResponses()["android"];
        $ytdata = json_decode($android);
        $tabs = $ytdata -> contents -> singleColumnBrowseResultsRenderer -> tabs;
        
        $continuation = "";
        for ($i = 0; $i < count($tabs); $i++) {
            if ($content = @$tabs[$i] -> tabRenderer -> content -> sectionListRenderer) {
                for ($i = 0; $i < count($content -> continuations); $i++) {
                    if ($temp = @$content -> continuations[$i] -> reloadContinuationData) {
                        $continuation = str_replace("%3D", "", $temp -> continuation);
                    }
                }
            }
        }

        $newContinuation = WebV2Shelves::continuationToWeb($continuation);

        Request::queueInnertubeRequest("wv2", "browse", (object) [
            "continuation" => $newContinuation
        ]);
        $wv2response = Request::getResponses()["wv2"];
        $wv2data = json_decode($wv2response);
        
        $response = RichShelfUtils::reformatResponse($wv2data);

        return $response;
    }

    /**
     * Build the gridded home page style (without categories)
     * 
     * TODO(dcooper): cleanup
     * 
     * @param object $yt  Reference to the global context (lazy)
     * @return void
     */
    private static function buildStyleGridded(&$yt) {
        $yt->page = (object) [];
        $yt->flow = (isset($_GET["flow"]) and $_GET["flow"] == "2") ? "list" : "grid";

        $response = Request::innertubeRequest(
            "browse", 
            (object)[
                "browseId" => self::BROWSE_ID
            ]
        );

        $ytdata = json_decode($response);
        $items = $ytdata -> contents -> twoColumnBrowseResultsRenderer -> tabs[0] -> tabRenderer -> content -> richGridRenderer -> contents;

        $yt -> response = $response;
        $yt -> videoList = [];

        for ($i = 0; $i < count($items); $i++)
        {
            if ($content = @$items[$i]->richItemRenderer->content)
            {
                if ("grid" == $yt->flow)
                {
                    foreach ($content as $name => $value)
                    {
                        // Convert name formatting
                        // videoRenderer => gridVideoRenderer
                        $name = "grid" . ucfirst($name);

                        $yt->videoList[] = (object)[$name => $value];
                        break;
                    }
                }
                else
                {
                    $yt->videoList[] = $content;
                }
            }
            else
            {
                $yt->videoList[] = $items[$i];
            }
        }

        $yt -> page -> continuation = end($yt -> videoList) -> continuationItemRenderer -> continuationEndpoint -> continuationCommand -> token ?? null;
    }
}

return new FeedWhatToWatchController();