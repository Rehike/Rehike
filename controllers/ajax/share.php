<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;
use \Rehike\TemplateFunctions;
use \Rehike\Model\Share\ShareBoxModel;

return new class extends AjaxController {
    public function onGet(&$yt, $request) {
        $action = self::findAction();

        if (is_null($action)) self::error();

        switch ($action) {
            case "get_share_box":
                self::getShareBox($yt, $request);
                break;
        }
    }

    /**
     * Get the share box.
     */
    private function getShareBox(&$yt, $request) {
        $this -> template = "ajax/share/get_share_box";
        if (!isset($request -> params -> video_id)) self::error();
        $videoId = $request -> params -> video_id;

        Request::queueInnertubeRequest("get_share_box", "next", (object) [
            "videoId" => $videoId
        ]);
        $response = Request::getResponses()["get_share_box"];
        $ytdata = json_decode($response);

        $results = $ytdata -> contents -> twoColumnWatchNextResults -> results -> results -> contents ?? [];
        for ($i = 0; $i < count($results); $i++) {
            if (isset($results[$i] -> videoPrimaryInfoRenderer)) {
                $priInfo = $results[$i] -> videoPrimaryInfoRenderer;
            }
        }

        if (isset($priInfo)) {
            $yt -> page = ShareBoxModel::bake($videoId, TemplateFunctions::getText($priInfo -> title));
        } else {
            http_response_code(400);
            echo $response;//"{\"errors\":[]}";
            die();
        }
    }
};