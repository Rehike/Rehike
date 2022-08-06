<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Model\Comments\CommentThread;
use \Rehike\Request;
use function YukisCoffee\getPropertyAtPath as getProp;

return new class extends AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        return true;
        $request = Request::innertubeRequest("comment/perform_comment_action", (object) [
            "actions" => [
                $_POST["action"]
            ]
        ]);

        $ytiResponse = json_decode($request);

        $response = (object)[];

        if ("STATUS_SUCCEEDED" != @$ytiResponse->actionResults[0]->status)
        {
            http_response_code(400);
        }

        $response->status = @$ytiResponse->actionResults[0]->status ?? "STATUS_FAILED";
    }
};