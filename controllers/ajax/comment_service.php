<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Model\Comments\CommentThread;
use \Rehike\Request;
use function YukisCoffee\getPropertyAtPath as getProp;

return new class extends AjaxController {
    public function onPost(&$yt, $request) {
        $action = self::findAction();
        if (!@$action) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => [
                    "Specify an action!"
                ]
            ]);
            die();
        }

        $this -> template = "ajax/comment_service/" . $action;
        $yt -> page = (object) [];
        $yt -> comments = (object) [];
        
        switch ($action) {
            case "create_comment":
                $response = Request::innertubeRequest("comment/create_comment", (object) [
                    "commentText" => $_POST["content"],
                    "createCommentParams" => $_POST["params"]
                ]);
                break;
            case "create_comment_reply":
                $response = Request::innertubeRequest("comment/create_comment_reply", (object) [
                    "commentText" => $_POST["content"],
                    "createReplyParams" => $_POST["params"]
                ]);
                break;
            case "get_comments":
            case "get_comment_replies":
                $response = Request::innertubeRequest("next", (object)[
                    "continuation" => $_POST["page_token"]
                ]);
                break;
        }
        $ytdata = json_decode($response);
        $yt -> response = $ytdata;

        $contPath = "onResponseReceivedEndpoints[0].appendContinuationItemsAction";
        $reloadPath = "onResponseReceivedEndpoints[1].reloadContinuationItemsCommand";
        switch ($action) {
            case "create_comment":
                $data = $ytdata -> actions[1] -> createCommentAction -> contents -> commentThreadRenderer ?? null;
                $yt -> comments = CommentThread::commentThreadRenderer($data);
                break;
            case "create_comment_reply":
                echo $response;
                die();
                $data = $ytdata -> actions[1] -> createCommentReplyAction -> contents -> commentRenderer ?? null;
                $yt -> comments = CommentThread::commentRenderer($data, true);
            case "get_comments":
                try {
                    $data = getProp($ytdata, $contPath);
                } catch (\YukisCoffee\GetPropertyAtPathException $e) {
                    try {
                        $data = getProp($ytdata, $reloadPath);
                    } catch(\YukisCoffee\GetPropertyAtPathException $e) {
                        echo json_encode((object) [
                            "error" => "Failed to get comment continuation/sort"
                        ]);
                        exit();
                    }
                }
                $yt -> comments = CommentThread::bakeComments(@$data);
                break;
            case "get_comment_replies":
                try {
                    $data = getProp($ytdata, $contPath);
                } catch(\YukisCoffee\GetPropertyAtPathException $e) {
                    echo json_encode((object) [
                        "error" => "Failed to get comment replies"
                    ]);
                    exit();
                }
                $yt -> comments = CommentThread::bakeReplies($data);
                break;
        }

    }
};