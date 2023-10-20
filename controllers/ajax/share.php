<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\Async\Promise;
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\TemplateFunctions;
use Rehike\Signin\API as SignIn;
use \Rehike\Model\Share\ShareBoxModel;
use \Rehike\Model\Share\ShareEmbedModel;
use \Rehike\Model\Share\ShareEmailModel;

use function Rehike\Async\async;

return new class extends AjaxController 
{
    private ?string $videoId;
    private ?string $listId;

    public function onGet(YtApp $yt, RequestMetadata $request): void 
    {
        $action = self::findAction();

        if (is_null($action) || !isset($request->params->video_id)) self::error();

        $this->videoId = $request->params->video_id;
        $this->listId = $request->params->list;

        switch ($action) 
        {
            case "get_share_box":
                self::getShareBox($yt, $request);
                break;
            case "get_embed":
                self::getEmbed($yt, $request);
                break;
            case "get_email":
                self::getEmail($yt, $request);
                break;
        }
    }

    /**
     * Get the share box.
     */
    private function getShareBox(YtApp $yt, RequestMetadata $request): Promise/*<void>*/
    {
        return async(function() use (&$yt, $request) {
            $this->template = "ajax/share/get_share_box";

            $priInfo = (yield self::videoInfo($this->videoId))->videoSecondaryInfoRenderer;

            $yt->page = ShareBoxModel::bake(
                videoId: $this->videoId, 
                title: TemplateFunctions::getText($priInfo->title), 
                listId: $this->listId
            );
        });
    }



    private function getEmbed(YtApp $yt, RequestMetadata $request): Promise/*<void>*/
    {
        return async(function() use (&$yt, $request) {

            $this->template = "ajax/share/get_embed";

            $priInfo = (yield self::videoInfo($this->videoId))->videoPrimaryInfoRenderer;
            $listData = null;

            if ($this->listId) 
            {
                $listData = yield Network::innertubeRequest(
                    action: "browse",
                    body: [
                        "browseId" => "VL" . $this->listId
                    ]
                );
            }

            $yt->page = ShareEmbedModel::bake(
                videoId: $this->videoId, 
                title: TemplateFunctions::getText($priInfo->title), 
                listData: $listData
            );
        });
    }



    private function getEmail(YtApp $yt, RequestMetadata $request) : Promise
    {
        return async(function() use (&$yt, $request) {

            $this->template = "ajax/share/get_email";

            $vidInfo = yield self::videoInfo($this->videoId);
            $priInfo = $vidInfo->videoPrimaryInfoRenderer;
            $secInfo = $vidInfo->videoSecondaryInfoRenderer;

            $userData = SignIn::getInfo();
            
            $yt->page = ShareEmailModel::bake(
                videoId: $this->videoId,
                title: TemplateFunctions::getText($priInfo->title),
                userId: $userData["ucid"],
                userName: $userData["activeChannel"]["name"],
                desc: $secInfo->attributedDescription->content
            );
        });
    }





    protected function videoInfo(string $videoId) : Promise/*<object>*/
    {
        return async(function() use ($videoId) {

            $response = yield Network::innertubeRequest(
                action: "next",
                body: [
                    "videoId" => $videoId
                ]
            );
            $ytdata = $response->getJson();
    
            $results = $ytdata->contents->twoColumnWatchNextResults->results->results->contents ?? [];


            $priInfo = null;
            $secInfo = null;
            foreach ($results as $infoItem) {
                $priInfo = $infoItem->videoPrimaryInfoRenderer ?? $priInfo;
                $secInfo = $infoItem->videoSecondaryInfoRenderer ?? $secInfo;
            }
    


            if (!isset($priInfo) || !isset($secInfo))  {
                echo $response;
                self::error();
            }
            
            // return $priInfo;
            return (object) [
                "videoPrimaryInfoRenderer" => $priInfo, 
                "videoSecondaryInfoRenderer" => $secInfo
            ];

        });
    }
};