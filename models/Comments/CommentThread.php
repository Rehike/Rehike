<?php
namespace Rehike\Model\Comments;

use YukisCoffee\PropertyAtPath;
use YukisCoffee\CoffeeRequest\Promise;
use \Rehike\Model\Comments\MCommentVoteButton as VoteButton;
use \Rehike\Model\Comments\MCommentReplyButton as ReplyButton;
use \Rehike\i18n;
use \Rehike\ConfigManager\ConfigManager;
use \Rehike\Network;

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

    public static $dataApiData = [];

    /**
     * Populate self::$dataApiData with channel data.
     * 
     * @param string[] $cids  List of channel IDs to get display names for.
     */
    public static function populateDataApiData(array $cids)
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

            foreach ($data->items as $item)
            {
                self::$dataApiData += [
                    $item->id => $item->snippet
                ];
            }
        });
    }

    public static function bakeComments($context): Promise
    {
        return new Promise(function ($resolve, $reject) use ($context) {
            // Top-level function
            // $context = continuation command

            $context = $context->continuationItems;
            
            $out = ["commentThreads" => []];

            $cids = [];
            foreach($context as $comment)
            {
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

            self::populateDataApiData($cids)
            ->then(function() use (&$context, &$out, $resolve) {
                if (@$context)
                {
                    for ($i = 0, $count = count($context); $i < $count; $i++) {
                        if (isset($context[$i]->commentThreadRenderer))
                        {
                            $out["commentThreads"][] = self::commentThreadRenderer(
                                $context[$i]->commentThreadRenderer
                            );
                        }
                        else if ($count - 1 == $i && isset($context[$i]->continuationItemRenderer))
                        {
                            $out += [
                                "commentContinuationRenderer" => self::commentContinuationRenderer(
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

    public static function bakeReplies($context): Promise
    {
        return new Promise(function ($resolve, $reject) use ($context) {
            // Top level function
            // $context = (Array containing all commentRenderer items)

            $context = @$context->continuationItems;

            $out = ["comments" => [], "repliesTargetId" => str_replace("comment-replies-item-", "", $context->targetId)];

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

            self::populateDataApiData($cids)
            ->then(function() use (&$context, &$out, $resolve) {        
                if ($context) for ($i = 0, $count = count($context); $i < $count; $i++)
                {
                    if (isset($context[$i]->commentRenderer))
                    {
                        $out["comments"][] = self::commentRenderer($context[$i]->commentRenderer, true);
                    }
                    else if ($count -1 == $i && isset($context[$i]->continuationItemRenderer))
                    {
                        $out += ["repliesContinuationRenderer" => self::repliesContinuationRenderer($context[$i]->continuationItemRenderer)];
                    }
                }

                $resolve($out);
            });
        });
    }
    
    public static function commentThreadRenderer($context)
    {
        $out = [];

        // PLEASE NOTE:
        // The extra preceding property "comment"/"replies" is removed by this.
        if (isset($context->comment)) {
            $out['commentRenderer'] = self::commentRenderer($context->comment->commentRenderer);
        }

        if (isset($context->replies)) {
            $out['commentRepliesRenderer'] = self::commentRepliesRenderer($context->replies->commentRepliesRenderer);
        }
        
        return ['commentThreadRenderer' => $out];
    }
    
    public static function commentRenderer($context, $isReply = false)
    {
        // Right now, the method is to modify a
        // standard InnerTube response.

        $context->isReply = $isReply;
        if (isset($context->voteCount)) self::addLikeCount($context);

        if ($data = @self::$dataApiData[$context->authorEndpoint->browseEndpoint->browseId]) {
            $context->authorText = (object) [
                "simpleText" => $data->title
            ];
        }

        // Eliminate surrounding spaces on channel mention and correct mentions
        foreach ($context->contentText->runs as &$run) {
            $run->text = str_replace("\u{00A0}", "", $run->text);

            if ($ucid = @$run->navigationEndpoint->browseEndpoint->browseId)
            {
                if (substr($ucid, 0, 2) == "UC"
                &&  isset(self::$dataApiData[$ucid]))
                {
                    $run->text = "@" . self::$dataApiData[$ucid]->title;
                }
            }
        }

        $context->likeButton = VoteButton::fromData(PropertyAtPath::get($context, self::LIKE_BUTTON_PATH));
        $context->dislikeButton = VoteButton::fromData(PropertyAtPath::get($context, self::DISLIKE_BUTTON_PATH));
        
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
    public static function commentRepliesRenderer($context)
    {
        if (isset($context->viewReplies))
        {
            $teaser = false /* ConfigManager::getConfigProp("appearance.teaserReplies") */;

            if (i18n::namespaceExists("comments")) {
                $i18n = i18n::getNamespace("comments");
            } else {
                $i18n = i18n::newNamespace("comments");
                $i18n->registerFromFolder("i18n/comments");
            }

            $viewText = &$context->viewReplies->buttonRenderer->text->runs[0]->text;
            $hideText = &$context->hideReplies->buttonRenderer->text->runs[0]->text;

            // YouTube is experimenting with bringing back the
            // old "View X replies" text format
            if (!preg_match($i18n->oldReplyTextRegex, $viewText)) {
                $replyCount = (int) preg_replace($i18n->replyCountIsolator, "", $viewText);
                if (isset($context->viewRepliesCreatorThumbnail)) {
                    $creatorName = $context->viewRepliesCreatorThumbnail->accessibility->accessibilityData->label;
                }

                if ($teaser && $replyCount < 3) {
                    unset($context->viewReplies);
                    unset($context->hideReplies);
                } else if ($replyCount > 1) {
                    if (isset($creatorName)) {
                        $viewText = $teaser
                        ? $i18n->viewMultiTeaserOwner($replyCount, $creatorName)
                        : $i18n->viewMultiOwner($replyCount, $creatorName);
                    } else {
                        $viewText = $teaser
                        ? $i18n->viewMultiTeaser($replyCount)
                        : $i18n->viewMulti($replyCount);
                    }
                } else {
                    if (isset($creatorName)) {
                        $viewText = $i18n->viewSingularOwner($creatorName);
                    } else {
                        $viewText = $i18n->viewSingular;
                    }
                }

                $hideText = ($replyCount > 1) ? $i18n->hideMulti($replyCount) : $i18n->hideSingular;
            }
        }

        /*
         * Process teaser contents.
         */
        if (isset($context->teaserContents)) foreach($context->teaserContents as $item)
        {
            if (isset($item->commentRenderer))
                $item->commentRenderer = self::commentRenderer($item->commentRenderer, true);
        }

        return $context;
    }

    public static function commentContinuationRenderer($context)
    {
        return $context->continuationEndpoint->continuationCommand;
    }

    public static function repliesContinuationRenderer($context)
    {
        $context = $context->button->buttonRenderer;
        return
            [
                "token" => $context->command->continuationCommand->token,
                "text" => $context->text
            ];
    }
    
    public static function addLikeCount(&$context)
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
        
        $count = (int)self::getLikeCountFromLabel($likeAriaLabel);

        if (@$context->isLiked) {
            $context->voteCount = [
                "indifferentText" => (string)--$count,
                "likedText" => (string)$count
            ];
        } else {
            $context->voteCount = [
                "indifferentText" => (string)$count,
                "likedText" => (string)++$count
            ];
        }
    }

    /**
     * TODO (kirasicecreamm): i18n
     */
    public static function getLikeCountFromLabel($label)
    {
        $i18n = i18n::getNamespace("comments");
        return preg_replace($i18n->likeCountIsolator, "", $label);
    }
}