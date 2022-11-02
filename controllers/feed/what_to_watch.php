<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Request;
use Rehike\RehikeConfigManager as Config;
use Rehike\Util\AndroidW2w15Parser;
use Rehike\Util\WebV2Shelves;
use Rehike\Util\RichShelfUtils;
use Rehike\Model\Feed\MFeedAppbarNav;

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
    const BROWSE_ID = 'FEwhat_to_watch';

    public $template = "feed/what_to_watch";

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint('browse', self::BROWSE_ID);
        $yt->enableFooterCopyright = true;
        $yt->appbar->nav = new MFeedAppbarNav(self::BROWSE_ID);

        $yt -> page -> content = self::buildHomepage();
    }

    /**
     * Build the categorised "shelves" home page style
     * 
     * @return void
     */
    private static function buildHomepage() {
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
}

return new FeedWhatToWatchController();