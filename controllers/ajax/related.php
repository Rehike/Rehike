<?php
namespace Rehike\Controller\ajax;

use Rehike\Controller\core\AjaxController;
use Rehike\Request;

/**
 * Related (watch) ajax controller
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @version 1.0.20220805
 */
class AjaxRelatedController extends AjaxController {
    public $useTemplate = true;
    public $template = "ajax/related";

    public function onGet(&$yt, $request) {
        return $this->onPost($yt, $request);
    }

    public function onPost(&$yt, $request) {
        $this->spfIdListeners = [
            '@masthead_search<data-is-crosswalk>',
            'watch-more-related'
        ];

        if (!isset($_GET["continuation"])) {
            die('{"name":"other"}');
        }

        $response = Request::innertubeRequest(
            "next",
            (object) [
                "continuation" => $_GET["continuation"]
            ]
        );
        $ytdata = json_decode($response);
        
        $yt->page->items = $ytdata->onResponseReceivedEndpoints[0]->appendContinuationItemsAction->continuationItems;
    }
}

return new AjaxRelatedController();