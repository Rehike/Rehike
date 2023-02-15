<?php
namespace Rehike\Controller\ajax;

use Rehike\Request;
use Rehike\Util\RichShelfUtils;

return new class extends \Rehike\Controller\core\AjaxController {
    public $template = "ajax/browse";

    public function onGet(&$yt, $request) {
        return $this -> onPost($yt, $request);
    }

    public function onPost(&$yt, $request) {
        if (!isset($request -> params -> continuation)) self::error();

        Request::queueInnertubeRequest("browse_ajax", "browse", (object) [
            "continuation" => $request -> params -> continuation
        ]);
        $ytdata = json_decode(Request::getResponses()["browse_ajax"]);

        if (isset($ytdata -> onResponseReceivedActions)) {
            foreach ($ytdata -> onResponseReceivedActions as $action) {
                if (isset($action -> appendContinuationItemsAction)) {
                    
                    foreach ($action -> appendContinuationItemsAction -> continuationItems as &$item) {
                        switch (true) {
                            case isset($item -> continuationItemRenderer):
                                $yt -> page -> continuation = $item -> continuationItemRenderer -> continuationEndpoint -> continuationCommand -> token;
                                break;
                            case isset($item -> richItemRenderer):
                                $item = RichShelfUtils::reformatShelfItem($item);
                                break;
                            case isset($item -> richSectionRenderer -> content -> richShelfRenderer):
                                $item = RichShelfUtils::reformatShelf($item);
                                break;
                        }
                    }
                    $yt -> page -> items = $action -> appendContinuationItemsAction -> continuationItems;
                }
            }
        } else self::error();

        $yt -> page -> target = $request -> params -> target_id;
        $yt -> page -> response = $ytdata;
    }
};