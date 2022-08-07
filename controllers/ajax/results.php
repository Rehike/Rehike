<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;

return new class extends AjaxController {
    public $template = "ajax/results";

    public function onGet(&$yt, $request) {
        $action = self::findAction();

        if (@$action != "continuation") {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => []
            ]);
            exit();
        }

        $yt -> page = (object) [];
        $yt -> page -> target = $_GET["target_id"];

        $response = Request::innertubeRequest("search", (object) [
            "continuation" => $_GET["continuation"] ?? null
        ]);
        $ytdata = json_decode($response);

        $yt -> page -> resultList = $ytdata -> onResponseReceievedCommands -> appendContinuationItemsAction -> continuationItems[0] -> itemSectionRenderer -> contents ?? null;
        $yt -> page -> continuation = end(@$yt -> page -> resultList) -> continuationItemRenderer -> continuationEndpoint -> continuationCommand -> token ?? null;
    }
};