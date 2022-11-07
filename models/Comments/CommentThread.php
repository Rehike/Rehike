<?php
namespace Rehike\Model\Comments;

use function YukisCoffee\getPropertyAtPath as getProp;
use \Rehike\Model\Comments\MCommentVoteButton as VoteButton;
use \Rehike\Model\Comments\MCommentReplyButton as ReplyButton;
use \Rehike\i18n;

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

    public static function bakeComments($context)
    {
        // Top-level function
        // $context = continuation command

        $context = @$context->continuationItems;
        
        $out = ["commentsThreads" => []];
        
        if ($context) for ($i = 0, $count = count($context); $i < $count; $i++) {
            if (isset($context[$i]->commentThreadRenderer))
            {
                $out["commentThreads"][] = self::commentThreadRenderer($context[$i]->commentThreadRenderer);
            }
            else if ($count - 1 == $i && isset($context[$i]->continuationItemRenderer))
            {
                $out += ["commentContinuationRenderer" => self::commentContinuationRenderer($context[$i]->continuationItemRenderer)];
            }
        }
        
        return $out;
    }

    public static function bakeReplies($context)
    {
        // Top level function
        // $context = (Array containing all commentRenderer items)

        $items = @$context->continuationItems;

        $out = ["comments" => [], "repliesTargetId" => str_replace("comment-replies-item-", "", $context->targetId)];

		if ($items) for ($i = 0, $count = count($items); $i < $count; $i++)
        {
            if (isset($items[$i]->commentRenderer))
            {
                $out["comments"][] = self::commentRenderer($items[$i]->commentRenderer, true);
            }
            else if ($count -1 == $i && isset($items[$i]->continuationItemRenderer))
            {
                $out += ["repliesContinuationRenderer" => self::repliesContinuationRenderer($items[$i]->continuationItemRenderer)];
            }
        }

        return $out;
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

        // Eliminate surrounding spaces on channel mention
        foreach ($context -> contentText -> runs as &$run) {
            $run -> text = str_replace("\u{00A0}", "", $run -> text);
        }

        $context->likeButton = VoteButton::fromData(getProp($context, self::LIKE_BUTTON_PATH));
        $context->dislikeButton = VoteButton::fromData(getProp($context, self::DISLIKE_BUTTON_PATH));
        $context->replyButton = ReplyButton::fromData(getProp($context, self::REPLY_BUTTON_PATH), $context -> commentId);

        try {
            $context->creatorHeart = getProp($context, self::HEART_BUTTON_PATH);
        } catch (\YukisCoffee\GetPropertyAtPathException $e) {
            $context->creatorHeart = null;
        } 
        

        return $context;
    }

    public static function commentRepliesRenderer($context)
    {
        /*
         * Process teaser contents.
         */
        if (isset($context->teaserContents)) foreach($context->teaserContents as $item)
        {
            if (isset($item->commentRenderer))
                $item->commentRenderer = self::commentRenderer($item->commentRenderer, true);
        }

        if (isset($context->viewReplies))
        {
            if (i18n::namespaceExists("comments")) {
                $i18n = i18n::getNamespace("comments");
            } else {
                $i18n = i18n::newNamespace("comments");
                $i18n -> registerFromFolder("i18n/comments");
            }

            $viewText = &$context->viewReplies->buttonRenderer->text->runs[0]->text;
            $hideText = &$context->hideReplies->buttonRenderer->text->runs[0]->text;

            $replyCount = (int) preg_replace($i18n -> replyCountIsolator, "", $viewText);
            if (isset($context -> viewRepliesCreatorThumbnail)) {
                $creatorName = $context -> viewRepliesCreatorThumbnail -> accessibility -> accessibilityData -> label;
            }

            if ($replyCount > 1) {
                if (isset($creatorName)) {
                    $viewText = $i18n -> viewMultiOwner($replyCount, $creatorName);
                } else {
                    $viewText = $i18n -> viewMulti($replyCount);
                }
            } else {
                if (isset($creatorName)) {
                    $viewText = $i18n -> viewSingularOwner($creatorName);
                } else {
                    $viewText = $i18n -> viewSingular;
                }
            }

            $hideText = ($replyCount > 1) ? $i18n -> hideMulti($replyCount) : $i18n -> hideSingular;
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

        $likeAriaLabel = getProp($context, 
            self::LIKE_BUTTON_PATH .
            ".accessibilityData." .
            self::COMMON_A11Y_LABEL
        );
        
        $count = (int)self::getLikeCountFromLabel($likeAriaLabel);

        if (@$context -> isLiked) {
            $context -> voteCount = [
                "indifferentText" => (string)--$count,
                "likedText" => (string)$count
            ];
        } else {
            $context -> voteCount = [
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
        return preg_replace("/(Like this )|(comment)|(reply)|( along with )|(,)|( other person)|(other people)/", "", $label);
    }
}