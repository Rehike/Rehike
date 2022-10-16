<?php
namespace Rehike\Controller;

use Rehike\Controller\core\NirvanaController;
use Rehike\Request;

/**
 * Guide builder (Browse channels) feed controller
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @version 1.0.20220805
 */
class FeedGuideBuilderController extends NirvanaController {
    public $template = 'feed/guide_builder';

    public function onGet(&$yt, $request) {
        $this->useJsModule('www/feed');
        $this->setEndpoint("browse", "FEguide_builder");
        
        $response = Request::innertubeRequest("browse", (object)[
            "browseId" => "FEguide_builder"
        ]);
        
        $ytdata = json_decode($response);
        
        $yt->page->data = $response;
        
        $shelvesList = $ytdata->contents->twoColumnBrowseResultsRenderer->
            tabs[0]->tabRenderer->content->sectionListRenderer->contents;
        
        $yt->page->shelvesList = $shelvesList;
    }
}

return new FeedGuideBuilderController();