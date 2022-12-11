<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\TemplateFunctions;
use \Rehike\Model\Share\ShareBoxModel;

/**
 * Controller for the share box AJAX.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
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
    private function getShareBox(&$yt, $request): void {
        $this -> template = "ajax/share/get_share_box";
        if (!isset($request -> params -> video_id)) self::error();
        $videoId = $request -> params -> video_id;

        Network::innertubeRequest(
            action: "next",
            body: [
                "videoId" => $videoId
            ]
        )->then(function ($response) use ($yt, $videoId) {
            $ytdata = $response->getJson();

            $results = ($ytdata -> contents -> twoColumnWatchNextResults 
                -> results -> results -> contents) ?? [];
            for ($i = 0; $i < count($results); $i++) {
                if (isset($results[$i] -> videoPrimaryInfoRenderer)) {
                    $primaryInfo = $results[$i] -> videoPrimaryInfoRenderer;
                }
            }

            if (isset($primaryInfo)) {
                $yt -> page = ShareBoxModel::bake(
                    videoId: $videoId, 
                    title: TemplateFunctions::getText($primaryInfo -> title)
                );
            } else {
                http_response_code(400);
                echo $response;//"{\"errors\":[]}";
                die();
            }
        });
    }
};