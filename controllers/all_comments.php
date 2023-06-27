<?php
use Rehike\Controller\core\NirvanaController;
use Rehike\Model\AllComments\AllCommentsModel;
use Rehike\Network;

use function Rehike\Async\async;

return new class extends NirvanaController
{
    public $template = "all_comments";

    public function onGet(&$yt, $request)
    {
        return async(function() use (&$yt, $request) {
            $this->useJsModule("www/watch");

            if (!isset($request->params->v))
                header("Location: /oops");

            $yt->videoId = $request->params->v;
            $response = yield Network::innertubeRequest("next", [
                "videoId" => $request->params->v
            ]);
            $wdata = $response->getJson();

            $results = $wdata->contents->twoColumnWatchNextResults->results->results->contents;

            // Invalid video ID
            if (isset($results[0]->itemSectionRenderer->contents[0]->backgroundPromoRenderer))
                header("Location: /oops");

            // To get the videoRenderer of the video
            $sresponse = yield Network::innertubeRequest("search", [
                "query" => $request->params->v,
                "params" => "QgIIAQ%253D%253D" // Ensure YouTube doesn't autocorrect the query
            ]);
            $sdata = $sresponse->getJson();

            
            $cdata = null;
            foreach ($results as $result)
            {
                if (@$result->itemSectionRenderer->targetId == "comments-section")
                {
                    $ctoken = $result->itemSectionRenderer->contents[0]->continuationItemRenderer->continuationEndpoint->continuationCommand->token;
                    $cresponse = yield Network::innertubeRequest("next", [
                        "continuation" => $ctoken
                    ]);
                    $yt->commentsToken = $ctoken;
                    $cdata = $cresponse->getJson();
                }
            }

            if ($cdata != null)
            {
                $yt->page = AllCommentsModel::bake($sdata, $cdata, $request->params->v);
            }
            else
            {
                header("Location: /oops");
            }
        });
    }
};