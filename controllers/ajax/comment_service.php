<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Model\Comments\CommentThread;
use \Rehike\Network;

/**
 * Controller for the primary comment service AJAX.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    public function onPost(&$yt, $request) {
        $action = self::findAction();
        if (!@$action) self::error();

        $yt->page = (object) [];
        
        switch ($action) {
            case "create_comment":
                self::createComment($yt);
                break;
            case "create_comment_reply":
                self::createCommentReply($yt);
                break;
            case "get_comments":
                self::getComments($yt);
                break;
            case "get_comment_replies":
                self::getCommentReplies($yt);
                break;
            case "perform_comment_action":
                self::performCommentAction();
                break;       
        }
    }

    /**
     * Create a comment.
     * 
     * @param $yt Template data.
     */
    private function createComment(&$yt) {
        $this->template = "ajax/comment_service/create_comment";

        $content = $_POST["content"] ?? null;
        $params = $_POST["params"] ?? null;
        
        // Reject invalid arguments.
        if((@$content == null) | (@$params == null))
        {
            self::error();
        }

        Network::innertubeRequest(
            action: "comment/create_comment",
            body: [
                "commentText" => $_POST["content"],
                "createCommentParams" => $_POST["params"]
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            $data = $ytdata->actions[1] ->createCommentAction->contents 
                ->commentThreadRenderer ?? null;
            
            if (null != $data) {
                $yt->page = CommentThread::commentThreadRenderer($data);
            } else {
                self::error();
            }
        });
    }

    /**
     * Create a reply to a comment.
     * 
     * @param $yt Template data.
     */
    private function createCommentReply(&$yt) {
        $this->template = "ajax/comment_service/create_comment_reply";

        $content = $_POST["content"] ?? null;
        $params = $_POST["params"] ?? null;
        
        // Reject invalid arguments.
        if((@$content == null) | (@$params == null))
        {
            self::error();
        }

        Network::innertubeRequest(
            action: "comment/create_comment_reply",
            body: [
                "commentText" => $_POST["content"],
                "createReplyParams" => $_POST["params"]
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            $data = $ytdata->actions[1] ->createCommentReplyAction 
                ->contents->commentRenderer ?? null;
            
            if (null != $data) {
                $yt->page = CommentThread::commentRenderer($data, true);
            } else {
                self::error();
            }
        });
    }

    /**
     * Get comments for continuation or
     * reload (for changing sort).
     * 
     * @param $yt Template data.
     */
    private function getComments(&$yt) {
        $this->template = "ajax/comment_service/get_comments";

        $ctoken = $_POST["page_token"] ?? null;
        if(!@$ctoken) self::error();

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_POST["page_token"]
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            foreach ($ytdata->onResponseReceivedEndpoints as $endpoint)
            {
                if ($a = $endpoint->appendContinuationItemsAction)
                {
                    $data = $a;
                } 
                else if ($a = $endpoint->reloadContinuationItemsCommand) 
                {
                    $data = $a;
                }
            }

            if (!is_null($data))
            {
                CommentThread::bakeComments($data)->then(function ($response)
                        use ($yt)
                {
                    $yt->page = $response;
                });
            }
            else
            {
                self::error();
            }
        });
    }

    /**
     * Get comment replies.
     * 
     * @param $yt Template data.
     */
    private function getCommentReplies(&$yt) {
        $this->template = "ajax/comment_service/get_comment_replies";

        $ctoken = $_POST["page_token"] ?? null;
        if (!@$ctoken) self::error();

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_POST["page_token"]
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            foreach ($ytdata->onResponseReceivedEndpoints as $endpoint) {
                if ($a = $endpoint->appendContinuationItemsAction) {
                    $data = $a;
                }
            }

            if (!is_null($data))
            {
                CommentThread::bakeReplies($data)->then(function ($response)
                        use ($yt)
                {
                    $yt->page = $response;
                });
            }
            else
            {
                self::error();
            }
        });
    }

    /**
     * Perform a comment action
     * (Like, dislike, heart, etc.)
     */
    private function performCommentAction() {
        $this->useTemplate = false;

        Network::innertubeRequest(
            action: "comment/perform_comment_action",
            body: [
                "actions" => [
                    $_POST["action"]
                ]
            ]
        )->then(function ($response) {
            $ytdata = $response->getJson();

            if (@$ytdata->actionResults[0] ->status == "STATUS_SUCCEEDED") {
                echo json_encode((object) [
                    "response" => "SUCCESS"
                ]);
            } else {
                self::error();
            }
        });
    }
};