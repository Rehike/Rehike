<?php
namespace Rehike\Controller\ajax;

use Rehike\Network;
use Rehike\Util\RichShelfUtils;

/**
 * Controller for browse AJAX requests.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends \Rehike\Controller\core\AjaxController {
    public $template = "ajax/browse";

    public function onGet(&$yt, $request) {
        return $this -> onPost($yt, $request);
    }

    public function onPost(&$yt, $request) {
        if (!isset($request -> params -> continuation)) self::error();

        Network::innertubeRequest(
            action: "browse",
            body: [
                "continuation" => $request -> params -> continuation
            ]
        )->then(function ($response) use ($yt, $request) {
            $ytdata = $response->getJson();

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
            } else {
                self::error();
            }
    
            $yt -> page -> target = $request -> params -> target_id;
            $yt -> page -> response = $ytdata;
        });
    }
};