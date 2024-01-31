<?php
namespace Rehike\Model\Comments;

use YukisCoffee\PropertyAtPath;
use YukisCoffee\CoffeeRequest\Promise;
use \Rehike\Model\Comments\MCommentVoteButton as VoteButton;
use \Rehike\Model\Comments\MCommentReplyButton as ReplyButton;
use \Rehike\i18n\i18n;
use \Rehike\ConfigManager\Config;
use \Rehike\Network;
use Rehike\Util\ParsingUtils;
use Rehike\ViewModelParser;

use function \Rehike\Async\async;

class CommentThread
{
    // Excepted structure is very similar to the InnerTube
    // comments next response, so this is very basic.
    const ACTIONS_PATH = "actionButtons.commentActionButtonsRenderer";
    const LIKE_BUTTON_PATH = self::ACTIONS_PATH . ".likeButton.toggleButtonRenderer";
    const DISLIKE_BUTTON_PATH = self::ACTIONS_PATH . ".dislikeButton.toggleButtonRenderer";
    const HEART_BUTTON_PATH = self::ACTIONS_PATH . ".creatorHeart.creatorHeartRenderer";
    const REPLY_BUTTON_PATH = self::ACTIONS_PATH . ".replyButton.buttonRenderer";
    const COMMON_A11Y_LABEL = "accessibilityData.label";

    protected object $data;
    protected array $dataApiData = [];

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Populate $dataApiData with channel data.
     * 
     * @param string[] $cids  List of channel IDs to get display names for.
     */
    public function populateDataApiData(array $cids)
    {
        return async(function() use ($cids) {
            $response = yield Network::dataApiRequest(
                action: "channels",
                params: [
                    "part" => "id,snippet",
                    "id" => implode(",", $cids)
                ]
            );
            $data = $response->getJson();

            if (isset($data->items))
            foreach ($data->items as $item)
            {
                $this->dataApiData += [
                    $item->id => $item->snippet
                ];
            }
        });
    }

    public function bakeComments(): Promise
    {
        $context = $this->getBaseCommentsContext();

        return new Promise(function ($resolve, $reject) use ($context) {
            $out = ["commentThreads" => []];

            $cids = [];
            foreach($context as $comment)
            {
                // FIX (kirasicecreamm): This loop (which is for collecting comment creator
                // UCIDS for requesting titles in Data API) didn't previously consider that
                // there may be a continuation at the end of the data, for example. This
                // fixes any warnings that may occur from that.
                if (!isset($comment->commentThreadRenderer->comment->commentRenderer))
                {
                    continue;
                }

                $commentr = $comment->commentThreadRenderer->comment->commentRenderer;

                if ($a = (@$commentr->authorEndpoint->browseEndpoint->browseId))
                {
                    if (!in_array($a, $cids))
                    $cids[] = $a;
                }

                foreach ($commentr->contentText->runs as $run)
                {
                    if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
                    {
                        if (!in_array($a, $cids))
                        $cids[] = $a;
                    }
                }

                if (isset($comment->commentThreadRenderer->replies->commentRepliesRenderer->teaserContents))
                foreach($comment->commentThreadRenderer->replies->commentRepliesRenderer->teaserContents as $teaser)
                {
                    $teaser = $teaser->commentRenderer;

                    if ($a = (@$teaser->authorEndpoint->browseEndpoint->browseId))
                    {
                        if (!in_array($a, $cids))
                        $cids[] = $a;
                    }

                    foreach ($teaser->contentText->runs as $run)
                    {
                        if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
                        {
                            if (!in_array($a, $cids))
                            $cids[] = $a;
                        }
                    }
                }
            }

            $this->populateDataApiData($cids)->then(function() use (&$context, &$out, $resolve) {
                if (is_countable($context))
                {
                    for ($i = 0, $count = count($context); $i < $count; $i++) {
                        if (isset($context[$i]->commentThreadRenderer))
                        {
                            $out["commentThreads"][] = $this->commentThreadRenderer(
                                $context[$i]->commentThreadRenderer
                            );
                        }
                        else if ($count - 1 == $i && isset($context[$i]->continuationItemRenderer))
                        {
                            $out += [
                                "commentContinuationRenderer" => $this->commentContinuationRenderer(
                                    $context[$i]->continuationItemRenderer
                                )
                            ];
                        }
                    }
                }

                $resolve($out);
            });
        });
    }

    public function bakeReplies($context): Promise
    {
        return new Promise(function ($resolve, $reject) use ($context) {
            // Top level function
            // $context = (Array containing all commentRenderer items)

            /*
             * WHY THE FUCK did you think it'd be a good idea to rename this variable from:
             *    $items = @$context->continuationItems;
             * to the less clear and infinitely more confusing:
             *    $context = @$context->continuationItems;
             * IGNORING pre-existing use of the original variable, and overriding it entirely,
             * thus breaking reply continuations for MONTHS?
             * 
             * For the record, the distinction was used for setting the target ID, which is required
             * for subsequent continautions. You can see this... IN THE VERY NEXT FUCKING LINE AFTER
             * THE DECLARATION OF THE VARIABLE THAT YOU RENAMED.
             * 
             * It was supposed to read the target ID of the top-level context (i.e. the $context ARGUMENT
             * TO THIS FUNCTION), but since the variable name now points to a DIFFERENT thing, it attempted
             * to read the targetId property from an object ON WHICH IT DOESN'T EXIST. As such, this variable
             * would become NULL and the templater would receive bad data for the continuation item (see
             * common/comments/replies_list.twig), so all reply continuations but the very first (show more
             * replies) simply stop working, even if the response seems to be perfectly legal (because it is).
             * 
             * Also see pre-rename: https://github.com/Rehike/Rehike/blob/a0c3783673be075ca13c75fda20c627be66f5630/models/Comments/CommentThread.php#L84-L90
             */
            $topLevelContext = $context;
            $context = @$context->continuationItems;

            // REPLIES TARGET ID MUST BE SET OR CONTINUATIONS WILL BREAK
            // DO NOT CHANGE CODE AFFECTING IT UNLESS YOU VERIFY CONTINUATIONS STILL WORK
            $out = ["comments" => [], "repliesTargetId" => str_replace("comment-replies-item-", "", $topLevelContext->targetId)];

            $cids = [];
            foreach($context as $comment)
            {
                if ($a = $comment->commentRenderer->authorEndpoint->browseEndpoint->browseId) 
                {
                    $cids[] = $a;
                }

                foreach ($comment->commentThreadRenderer->comment->commentRenderer->contentText->runs as $run)
                {
                    if ($a = @$run->navigationEndpoint->browseEndpoint->browseId)
                    {
                        if (!in_array($a, $cids))
                        $cids[] = $a;
                    }
                }
            }

            $this->populateDataApiData($cids)->then(function() use (&$context, &$out, $resolve) {
                if ($context) for ($i = 0, $count = count($context); $i < $count; $i++)
                {
                    if (isset($context[$i]->commentRenderer))
                    {
                        $out["comments"][] = $this->commentRenderer($context[$i]->commentRenderer, true);
                    }
                    else if ($count -1 == $i && isset($context[$i]->continuationItemRenderer))
                    {
                        $out += ["repliesContinuationRenderer" => $this->repliesContinuationRenderer($context[$i]->continuationItemRenderer)];
                    }
                }

                $resolve($out);
            });
        });
    }

    private function getBaseCommentsContext(): mixed
    {
        return $this->data->onResponseReceivedEndpoints[1]->reloadContinuationItemsCommand->continuationItems;
    }

    public function commentThreadRenderer($context)
    {
        $out = [];

        // 2024/01/30: Unwrap viewmodels if the experiment is active:
        if (isset($context->commentViewModel->commentViewModel))
        {
            $rendererData = $this->convertViewModel($context->commentViewModel->commentViewModel);
        }
        else
        {
            $rendererData = $context->comment->commentRenderer;
        }

        // PLEASE NOTE:
        // The extra preceding property "comment"/"replies" is removed by this.
        if (isset($context->comment)) {
            $out['commentRenderer'] = $this->commentRenderer($context->comment->commentRenderer);
        }

        if (isset($context->replies)) {
            $out['commentRepliesRenderer'] = $this->commentRepliesRenderer($context->replies->commentRepliesRenderer);
        }
        
        return ['commentThreadRenderer' => $out];
    }

    public function commentRenderer($context, $isReply = false)
    {
        // Right now, the method is to modify a
        // standard InnerTube response.

        $context->isReply = $isReply;

        if ($data = @$this->dataApiData[$context->authorEndpoint->browseEndpoint->browseId]) {
            $context->authorText = (object) [
                "simpleText" => $data->title
            ];
        }

        // Correct mentions
        foreach ($context->contentText->runs as $i => &$run) {
            if ($ucid = @$run->navigationEndpoint->browseEndpoint->browseId)
            {
                /** 
                 * Redo the whole @ string. This also removes the automatic spaces
                 * put around it.
                 */
                if (substr($ucid, 0, 2) == "UC"
                &&  isset($this->dataApiData[$ucid]))
                {
                    $run->text = "@" . $this->dataApiData[$ucid]->title . "";
                }

                /**
                 * Add a space to the next run if it isn't there. We need to do this
                 * or else some comments will show things like: "@userHi hello".
                 */
                $nextRun = &$context->contentText->runs[$i + 1];
                if ($nextRun && substr($nextRun->text, 0, 1) != " ")
                {
                    $nextRun->text = " " . $nextRun->text;
                }
            }
        }

        $context->likeButton = VoteButton::fromData(PropertyAtPath::get($context, self::LIKE_BUTTON_PATH));
        $context->dislikeButton = VoteButton::fromData(PropertyAtPath::get($context, self::DISLIKE_BUTTON_PATH));
		if (isset($context->voteCount)) $this->addLikeCount($context);
		
        try {
            $context->replyButton = ReplyButton::fromData(PropertyAtPath::get($context, self::REPLY_BUTTON_PATH), $context->commentId);
        } catch(\YukisCoffee\PropertyAtPathException $e) {
            $context->replyButton = null;
        }

        try {
            $context->creatorHeart = PropertyAtPath::get($context, self::HEART_BUTTON_PATH);
        } catch (\YukisCoffee\PropertyAtPathException $e) {
            $context->creatorHeart = null;
        } 
        

        return $context;
    }

    // WHAT THE FUCK
    public function commentRepliesRenderer($context)
    {
        if (isset($context->viewReplies))
        {
            $teaser = false /* Config::getConfigProp("appearance.teaserReplies") */;

            $i18n = i18n::getNamespace("comments");

            $viewText = &$context->viewReplies->buttonRenderer->text->runs[0]->text;
            $hideText = &$context->hideReplies->buttonRenderer->text->runs[0]->text;

            // YouTube is experimenting with bringing back the
            // old "View X replies" text format
            // TODO (ev): Is this still applicable? The above comment was written on 15 Nov 2022.
            //            I think it is appropriate to remove this comment and revise the behaviour
            //            otherwise.
            if (!preg_match($i18n->get("oldReplyTextRegex"), $viewText)) {
                $replyCount = (int) preg_replace($i18n->get("replyCountIsolator"), "", $viewText);
                if (isset($context->viewRepliesCreatorThumbnail)) {
                    $creatorName = $context->viewRepliesCreatorThumbnail->accessibility->accessibilityData->label;
                }

                if ($teaser && $replyCount < 3) {
                    unset($context->viewReplies);
                    unset($context->hideReplies);
                } else if ($replyCount > 1) {
                    if (isset($creatorName)) {
                        $viewText = $teaser
                        ? $i18n->format("viewMultiTeaserOwner", $replyCount, $creatorName)
                        : $i18n->format("viewMultiOwner", $replyCount, $creatorName);
                    } else {
                        $viewText = $teaser
                        ? $i18n->format("viewMultiTeaser", $replyCount)
                        : $i18n->format("viewMulti", $replyCount);
                    }
                } else {
                    if (isset($creatorName)) {
                        $viewText = $i18n->format("viewSingularOwner", $creatorName);
                    } else {
                        $viewText = $i18n->get("viewSingular");
                    }
                }

                $hideText = ($replyCount > 1)
                    ? $i18n->format("hideMulti", $replyCount)
                    : $i18n->get("hideSingular");
            }
        }

        /*
         * Process teaser contents.
         */
        if (isset($context->teaserContents)) foreach($context->teaserContents as $item)
        {
            if (isset($item->commentRenderer))
                $item->commentRenderer = $this->commentRenderer($item->commentRenderer, true);
        }

        return $context;
    }

    private function commentContinuationRenderer($context)
    {
        return $context->continuationEndpoint->continuationCommand;
    }

    private function repliesContinuationRenderer($context)
    {
        $context = $context->button->buttonRenderer;
        return
            [
                "token" => $context->command->continuationCommand->token,
                "text" => $context->text
            ];
    }

    /**
     * Converts comment view models to the old format, which Rehike uses for parsing.
     *
     * View models are these insane pieces of shit that the monkeys who work on InnerTube
     * decided to adopt today. Instead of the API being designed in any remotely sane way,
     * because InnerTube was too good, they decided to rewrite it to be more scattered and
     * messier.
     *
     * In the case that we're using a view model, we have to look up its mutation data and
     * reform the clean object ourselves. Fuck you, you cunts.
     */
    private function convertViewModel(object $context): object
    {
        $parser = new ViewModelParser($this->data);

        $entData = $parser->getViewModelEntities($context, [
            "commentKey" => "comment",
            "sharedKey" => "shared",
            "toolbarStateKey" => "toolbarState",
            "toolbarSurfaceKey" => "toolbarSurface",
            "commentSurfaceKey" => "commentSurface"
        ]);



        // To add insult to injury, they also moved comment text to use commandRuns.
        // I call for a firebombing on YouTube's offices tbh
        $commentText = ParsingUtils::commandRunsToRuns($entData["comment"]->payload->properties->content);
        $publishedTime = $entData["comment"]->payload->properties->publishedTime;
        $commentId = $entData["comment"]->payload->properties->commentId;

        $isLiked = $entData["toolbarState"]->payload->engagementToolbarStateEntityPayload->likeState != "TOOLBAR_LIKE_STATE_INDIFFERENT";
        $isHearted = $entData["toolbarState"]->payload->engagementToolbarStateEntityPayload->heartState != "TOOLBAR_HEART_STATE_UNHEARTED";


    }
    
    private function addLikeCount(&$context)
    {
        // Adds to context:
        /*
         {
            "voteCount": {
                "indifferentText": "2" | (string) Spliced count from accessibility label
                "likedText": "3" | (string) => (int)++indifferentText
            }
         }
        */

        $likeAriaLabel = PropertyAtPath::get($context,
            self::LIKE_BUTTON_PATH .
            ".accessibilityData." .
            self::COMMON_A11Y_LABEL
        );
        
        $count = (int)$this->getLikeCountFromLabel($likeAriaLabel);
		
		if (@$context->likeButton->checked) {
			$context->voteCount = [
				"indifferentText" => (string)($count - 1),
				"likedText" => (string)$count
			];
		} else {
			$context->voteCount = [
				"indifferentText" => (string)$count,
				"likedText" => (string)($count + 1)
			];
		}
    }

    private function getLikeCountFromLabel($label)
    {
        $i18n = i18n::getNamespace("comments");
        return preg_replace($i18n->get("likeCountIsolator"), "", $label);
    }
}