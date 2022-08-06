<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Request;
use Rehike\RehikeConfigManager as cfg;

// used by shelves style
require "controllers/utils/AndroidW2w15Parser.php";

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
    const STYLE_GRIDDED_TEMPLATE = 'feed/what_to_watch_v2';

    public $template;

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint('browse', self::BROWSE_ID);
        $yt->enableFooterCopyright = true;

        // get style
        if (cfg::getConfigProp('useWebV2HomeEndpoint' ?? false) /* v2 if true, else shelves */) {
            $this->template = self::STYLE_GRIDDED_TEMPLATE;

            self::buildStyleGridded($yt);
        } else {
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
        /**
         * BUG (yukiscoffee): WEB what_to_watch shelves API is
         * down (permanently?). However, ANDROID shelves have
         * a similar markup and are still up.
         */

        $response = Request::innertubeRequest(
            "browse", 
            (object)[
                "browseId" => self::BROWSE_ID
            ],
            "ANDROID",
            "15.14.33"
        );

        $ytdata = json_decode($response);

        $shelvesList = $ytdata->contents->singleColumnBrowseResultsRenderer->
            tabs[0]->tabRenderer->content->sectionListRenderer->contents;


        /** Continuations are still buggy */
        $continuation = $ytdata->contents->singleColumnBrowseResultsRenderer->tabs[0]->tabRenderer->content->sectionListRenderer->continuations[0]->nextContinuationData->continuation;

        $shelvesList = \AndroidW2w15Parser::parse($shelvesList);

        // begin response
        $response = (object) [];
        
        if (isset($shelvesList))  $response->shelvesList = &$shelvesList;
        if (isset($continuation)) $response->continuation = &$continuation;

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