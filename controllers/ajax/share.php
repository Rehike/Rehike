<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;
use \Rehike\TemplateFunctions;
use \Rehike\Model\Share\ShareBoxModel;
use \Rehike\Model\Share\ShareEmbedModel;

return new class extends AjaxController {

    public function onGet(&$yt, $request) {
        $action = self::findAction();

        if (is_null($action) || !isset($request->params->video_id)) self::error();

        $this->videoId = $request->params->video_id;
        $this->listId = $request->params->list;

        switch ($action) {
            case "get_share_box":
                self::getShareBox($yt, $request);
                break;
            case "get_embed":
                self::getEmbed($yt, $request);
                break;
        }
    }



    /**
     * Get the share box.
     */
    private function getShareBox(&$yt, $request) {
        $this->template = "ajax/share/get_share_box";
        $priInfo = self::videoInfo("get_share_box", $this->videoId);
        $yt->page = ShareBoxModel::bake($this->videoId, TemplateFunctions::getText($priInfo->title), $this->listId);
    }



    private function getEmbed(&$yt, $request) {
        $this->template = "ajax/share/get_embed";
        $priInfo = self::videoInfo("get_embed", $this->videoId);
        $listData = null;

        if ($this->listId) {
            Request::queueInnertubeRequest("get_embed_playlist", "browse", (object) [
                "browseId" => "VL" . $this->listId
            ]);
            $listData = json_decode(Request::getResponses()["get_embed_playlist"]);
        }

        $yt->page = ShareEmbedModel::bake($this->videoId, TemplateFunctions::getText($priInfo->title), $listData);
    
    }




    protected function videoInfo($id, $videoId) {

        Request::queueInnertubeRequest($id, "next", (object) [
            "videoId" => $videoId
        ]);
        $response = Request::getResponses()[$id];
        $ytdata = json_decode($response);

        $results = $ytdata->contents->twoColumnWatchNextResults->results->results->contents ?? [];
        for ($i = 0; $i < count($results); $i++) {
            if (isset($results[$i]->videoPrimaryInfoRenderer)) {
                $priInfo = $results[$i]->videoPrimaryInfoRenderer;
            }
        }

        if (!isset($priInfo)) {
            http_response_code(400);
            echo $response;//"{\"errors\":[]}";
            die();
        }
        return $priInfo;

    }

};
