<?php
namespace Rehike\Controller\ajax;

use Rehike\Model\ViewModelConverter\CommentsViewModelConverter;
use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

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
return new class extends AjaxController
{
    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();
        if (!@$action) self::error();

        $yt->page = (object) [];
        
        switch ($action)
        {
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
    private function createComment(YtApp $yt): void
    {
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
                "createCommentParams" => $_POST["params"],
                "hl" => "de_DE"
            ]
        )->then(function ($response) use (&$yt) {
            $ytdata = $response->getJson();


            $renderer = null;
            foreach($ytdata->actions as $action)
            {
                if (!isset($action->createCommentAction)) continue;
                $renderer = $action->createCommentAction->contents->commentThreadRenderer;
            }

            if (isset($renderer->commentViewModel->commentViewModel))
            {
                $converter = new CommentsViewModelConverter(
                    $renderer->commentViewModel->commentViewModel,
                    $ytdata->frameworkUpdates
                );
                $renderer = (object) [
                    "comment" => (object) [
                        "commentRenderer" => $converter->bakeCommentRenderer()
                    ]
                ];
            }

            $data = $renderer->comment->commentRenderer;


            $cids = [];
            $cids[] = $data->authorEndpoint->browseEndpoint->browseId;

            foreach ($data->contentText->runs as $run)
            {
                if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
                {
                    if (!in_array($a, $cids))
                    $cids[] = $a;
                }
            }

            $commentsBakery = new CommentThread($ytdata);

            $commentsBakery->populateDataApiData($cids)->then(function() use (&$yt, $renderer, $commentsBakery) {
                if (null != $renderer)
                {
                    $yt->page->comment = $commentsBakery->commentThreadRenderer($renderer);
                }
                else
                {
                    self::error();
                }
            });
        });
    }

    /**
     * Create a reply to a comment.
     * 
     * @param $yt Template data.
     */
    private function createCommentReply(YtApp $yt): void
    {
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
        )->then(function ($response) use (&$yt) {
            $ytdata = $response->getJson();


            $renderer = null;
            foreach($ytdata->actions as $action)
            {
                if (!isset($action->createCommentReplyAction)) continue;
                $renderer = $action->createCommentReplyAction->contents;
            }

            if (isset($renderer->commentViewModel))
            {
                $converter = new CommentsViewModelConverter(
                    $renderer->commentViewModel,
                    $ytdata->frameworkUpdates
                );
                $renderer = (object) [
                    "comment" => (object) [
                        "commentRenderer" => $converter->bakeCommentRenderer()
                    ]
                ];
            }

            $data = $renderer->comment->commentRenderer;


            $cids = [];
            $cids[] = $data->authorEndpoint->browseEndpoint->browseId;

            foreach ($data->contentText->runs as $run)
            {
                if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
                {
                    if (!in_array($a, $cids))
                        $cids[] = $a;
                }
            }

            $commentsBakery = new CommentThread($ytdata);

            $commentsBakery->populateDataApiData($cids)->then(function() use (&$yt, $renderer, $commentsBakery) {
                if (null != $renderer)
                {
                    $yt->page->comment = $commentsBakery->commentRenderer($renderer);
                }
                else
                {
                    self::error();
                }
            });
        });
    }


    /**
     * Get comments for continuation or
     * reload (for changing sort).
     * 
     * @param $yt Template data.
     */
    private function getComments(YtApp $yt): void
    {
        $this->template = "ajax/comment_service/get_comments";

        $ctoken = $_POST["page_token"] ?? null;
        if(!@$ctoken) self::error();

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_POST["page_token"],
                // We use German as a base language because it has full counts
                "hl" => "de_DE"
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            foreach ($ytdata->onResponseReceivedEndpoints as $endpoint)
            {
                if ($a = $endpoint->appendContinuationItemsAction)
                {
                    $data = $a?->continuationItems;
                } 
                else if ($a = $endpoint->reloadContinuationItemsCommand) 
                {
                    $data = $a?->continuationItems;
                }
            }

            if (!is_null($data))
            {
                $commentsBakery = new CommentThread($ytdata);
                $commentsBakery->bakeComments($data)->then(function ($response) use ($yt)
                {
                    $yt->page = (object)$response;
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
    private function getCommentReplies(YtApp $yt): void
    {
        $this->template = "ajax/comment_service/get_comment_replies";

        $ctoken = $_POST["page_token"] ?? null;
        if (!@$ctoken) self::error();

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $_POST["page_token"],
                // We use German as a base language because it has full counts
                "hl" => "de_DE"
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();

            foreach ($ytdata->onResponseReceivedEndpoints as $endpoint)
            {
                if ($a = $endpoint->appendContinuationItemsAction)
                {
                    $data = $a;
                }
            }

            if (!is_null($data))
            {
                $commentsBakery = new CommentThread($ytdata);
                $commentsBakery->bakeReplies($data)->then(function ($response) use ($yt)
                {
                    $yt->page = (object)$response;
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
    private function performCommentAction(): void
    {
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

            if (@$ytdata->actionResults[0]->status == "STATUS_SUCCEEDED")
            {
                echo json_encode((object) [
                    "response" => "SUCCESS"
                ]);
            }
            else
            {
                self::error();
            }
        });
    }
};