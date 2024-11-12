<?php
namespace Rehike\Controller\ajax;

use Rehike\Async\Promise;
use function Rehike\Async\async;

use Rehike\Model\ViewModelConverter\CommentsViewModelConverter;
use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Rehike\Controller\core\AjaxController;
use Rehike\Helper\CommentsContinuation;
use \Rehike\Model\Comments\CommentThread;
use \Rehike\Network;

/**
 * Controller for the primary comment service AJAX.
 *
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController
{
    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $action = $this->findAction();
        if (!@$action) self::error();

        $yt->page = (object) [];
        
        switch ($action)
        {
            case "create_comment":
                $this->createComment($yt);
                break;
            case "create_comment_reply":
                $this->createCommentReply($yt);
                break;
            case "get_comments":
                $this->getComments($yt);
                break;
            case "get_comment_replies":
                $this->getCommentReplies($yt);
                break;
            case "perform_comment_action":
                $this->performCommentAction();
                break;
            case "update_comment":
                $this->updateComment($yt);
                break;
            case "update_comment_reply":
                $this->updateCommentReply($yt);
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
                $this->renderCommentFromResponse($renderer->commentViewModel->commentViewModel, $ytdata);
            }
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
                $this->renderCommentFromResponse($renderer->commentViewModel, $ytdata);
            }
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
        
        $customToken = null;
        
        if (CommentsContinuation::isCustom($ctoken))
        {
            $customToken = CommentsContinuation::parse($ctoken);
            $ctoken = $customToken->originalContinuation;
        }

        Network::innertubeRequest(
            action: "next",
            body: [
                "continuation" => $ctoken,
                // We use German as a base language because it has full counts
                "hl" => "de_DE"
            ]
        )->then(function ($response) use ($yt, $customToken) {
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
                
                if (!is_null($customToken))
                {
                    $commentsBakery->supplyDisplayNameMap($customToken->displayNameMap);
                }
                
                $commentsBakery->bakeReplies($data)->then(function ($response) use ($yt)
                {
                    $yt->page = (object)$response;
                })->catch(function ($exception)
                {
                    echo json_encode((object)[
                        "errors" => [$exception->getMessage()]
                    ]);
                    \Rehike\Boot\Bootloader::doEarlyShutdown();
                });
            }
            else
            {
                // TODO: This causes an unhandled promise error.
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
    
    /**
     * Reconstruct a comment view model from mutation entities in response data.
     */
    private function reconstructCommentViewModel(object $ytdata): object
    {
        // We're only given a mutation set, so we have to regenerate the comment renderer
        // from this data.
        $commentViewModel = (object)[];
        
        foreach ($ytdata->frameworkUpdates->entityBatchUpdate->mutations as $mutation)
        {
            $entityKey = $mutation->entityKey;
            
            if (isset($mutation->payload->commentEntityPayload))
            {
                $commentViewModel->commentKey = $entityKey;
            }
            else if (isset($mutation->payload->engagementToolbarStateEntityPayload))
            {
                $commentViewModel->toolbarStateKey = $entityKey;
            }
            else if (isset($mutation->payload->engagementToolbarSurfaceEntityPayload))
            {
                $commentViewModel->toolbarSurfaceKey = $entityKey;
            }
            else if (isset($mutation->payload->commentSurfaceEntityPayload))
            {
                $commentViewModel->commentSurfaceKey = $entityKey;
            }
            else if (isset($mutation->payload->commentPinnedStateEntityPayload))
            {
                // I have no idea where this is exposed in CommentViewModel
            }
        }
        
        return $commentViewModel;
    }
    
    private function renderCommentFromResponse(object $commentViewModel, object $ytdata): Promise/*<>*/
    {
        $yt = YtApp::getInstance();
        
        $converter = new CommentsViewModelConverter(
            $commentViewModel,
            $ytdata->frameworkUpdates
        );
        $renderer = (object) [
            "comment" => (object) [
                "commentRenderer" => $converter->bakeCommentRenderer()
            ]
        ];
        
        $data = $renderer->comment->commentRenderer;
        
        $cids = [];
        $cids[] = $data->authorEndpoint->browseEndpoint->browseId;
        
        foreach ($data->contentText->runs as $run)
        {
            if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
            {
                if (!in_array($a, $cids))
                {
                    $cids[] = $a;
                }
            }
        }
        
        $commentsBakery = new CommentThread($ytdata);
        
        return $commentsBakery->ensureDisplayNamesAvailable($cids)->then(function() use (&$yt, $renderer, $commentsBakery) {
            if (null != $renderer)
            {
                $yt->page->comment = $commentsBakery->commentThreadRenderer($renderer);
            }
            else
            {
                self::error();
            }
        });
    }
    
    /**
     * Edit a comment.
     */
    private function updateComment(YtApp $yt): void
    {
        // This doesn't behave any different to creating a comment, so we reuse
        // the same template.
        $this->template = "ajax/comment_service/update_comment";
        
        /*/
        Network::innertubeRequestFake(
             localFilePath: "aa.json",
        /*/
        Network::innertubeRequest(
        //*/
            action: "comment/update_comment",
            body: [
                "commentText" => $_POST["content"],
                "updateCommentParams" => $_POST["params"],
                "hl" => "de_DE",
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();
            
            foreach ($ytdata->actions as $action)
            {
                if (!isset($action->updateCommentAction))
                {
                    continue;
                }
                else
                {
                    $updateCommentAction = $action->updateCommentAction;
                }
            }
            
            echo $action->actionResult->status;
            
            if (!isset($updateCommentAction) || $updateCommentAction->actionResult->status != "STATUS_SUCCEEDED")
            {
                self::error();
            }
            
            $commentViewModel = $this->reconstructCommentViewModel($ytdata);

            $this->renderCommentFromResponse($commentViewModel, $ytdata);
        });
    }
    
    /**
     * Edit a comment reply.
     */
    private function updateCommentReply(YtApp $yt): void
    {
        $this->template = "ajax/comment_service/update_comment_reply";
        
        Network::innertubeRequest(
            action: "comment/update_comment_reply",
            body: [
                "replyText" => $_POST["content"],
                "updateReplyParams" => $_POST["params"],
                "hl" => "de_DE",
            ]
        )->then(function ($response) use ($yt) {
            $ytdata = $response->getJson();
            
            foreach ($ytdata->actions as $action)
            {
                if (!isset($action->updateCommentReplyAction))
                {   
                    continue;
                }
                else
                {
                    $updateCommentAction = $action->updateCommentReplyAction;
                }
            }
            
            if (!isset($updateCommentAction) || $updateCommentAction->actionResult->status != "STATUS_SUCCEEDED")
            {
                self::error();
            }
            
            $commentViewModel = $this->reconstructCommentViewModel($ytdata);
            
            $this->renderCommentFromResponse($commentViewModel, $ytdata);
        });
    }
};