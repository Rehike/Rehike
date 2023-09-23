<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Controller\core\AjaxController;
use Rehike\Network;

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
class AjaxRelatedController extends AjaxController
{
    public bool $useTemplate = true;
    public string $template = "ajax/related";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $this->onPost($yt, $request);
    }

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $this->spfIdListeners = [
            '@masthead_search<data-is-crosswalk>',
            'watch-more-related'
        ];

        if (!isset($_GET["continuation"]))
        {
            die('{"name":"other"}');
        }

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_GET["continuation"]
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            $yt->page->items = $ytdata
                ->onResponseReceivedEndpoints[0]
                ->appendContinuationItemsAction
                ->continuationItems;
        });
    }
}

return new AjaxRelatedController();