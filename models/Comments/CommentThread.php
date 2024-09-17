<?php
namespace Rehike\Model\Comments;

use YukisCoffee\PropertyAtPath;
use YukisCoffee\CoffeeRequest\Promise;

// These are aliased to be a bit shorter.
use \Rehike\Model\Comments\{
    MCommentVoteButton as MVoteButton,
    MCommentReplyButton as MReplyButton
};

use \Rehike\i18n\i18n;
use \Rehike\ConfigManager\Config;
use Rehike\Helper\CommentsContinuation;
use Rehike\Model\ViewModelConverter\CommentsViewModelConverter;
use \Rehike\Network;
use Rehike\Util\ParsingUtils;
use Rehike\ViewModelParser;

use function \Rehike\Async\async;

/**
 * Bakery for comments.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella <kawapure@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
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
    protected DisplayNameManager $displayNameManager;

    public function __construct(object $data)
    {
        $this->data = $data;
        $this->displayNameManager = new DisplayNameManager();
    }
    
    public function getDisplayNameManager(): DisplayNameManager
    {
        return $this->displayNameManager;
    }
    
    public function supplyDisplayNameMap(object $displayNameMap): void
    {
        $this->getDisplayNameManager()->supplyDisplayNameMap($displayNameMap);
    }
    
    public function ensureDisplayNamesAvailable(array $cids): Promise/*<void>*/
    {
        return $this->getDisplayNameManager()->ensureDataAvailable($cids);
    }
    
    public function createDisplayNameMap(): object
    {
        return $this->getDisplayNameManager()->createDisplayNameMap();
    }
    
    public function getDisplayName(string $ucid): ?string
    {
        return $this->getDisplayNameManager()->getDisplayName($ucid);
    }

    public function bakeComments($context): Promise
    {
        // Account for view model update:
        $this->convertThreadsIfNecessary($context);

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

            $this->ensureDisplayNamesAvailable($cids)->then(function() use (&$context, &$out, $resolve) {
                if (is_countable($context))
                {
                    for ($i = 0, $count = count($context); $i < $count; $i++)
                    {
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
        // Account for view model update:
        $this->convertCommentsIfNecessary($context->continuationItems);

        return new Promise(function ($resolve, $reject) use ($context) {
            // Top level function
            // $context = (Array containing all commentRenderer items)

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

            $this->ensureDisplayNamesAvailable($cids)->then(function() use (&$context, &$out, $resolve) {
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

    private function convertThreadsIfNecessary(array &$threads): void
    {
        foreach ($threads as &$thread)
        {
            if (isset($thread->commentThreadRenderer->commentViewModel->commentViewModel))
            {
                $target = $thread->commentThreadRenderer->commentViewModel->commentViewModel;
                $renderer = $this->convertCommentViewModel($target);
                
                unset($thread->commentThreadRenderer->commentViewModel);
                $thread->commentThreadRenderer->comment = (object)[
                    "commentRenderer" => $renderer
                ];
            }
            
            // Comment teasers (i.e. linked replies)
            if (isset($thread->commentThreadRenderer->replies->commentRepliesRenderer->teaserContents))
            {
                $teaserReplies = $thread->commentThreadRenderer->replies->commentRepliesRenderer->teaserContents;
                $this->convertCommentsIfNecessary($teaserReplies);
            }

            // Thread replies (if they exist here):
            if (isset($thread->commentThreadRenderer->replies->commentRepliesRenderer->contents))
            {
                $replies = $thread->commentThreadRenderer->replies->commentRepliesRenderer->contents;
                $this->convertCommentsIfNecessary($replies);
            }
        }
    }

    private function convertCommentsIfNecessary(array &$comments): void
    {
        foreach ($comments as &$comment)
        {
            if (isset($comment->commentViewModel))
            {
                $target = $comment->commentViewModel;
                $renderer = $this->convertCommentViewModel($target);

                unset($comment->commentViewModel);
                $comment->commentRenderer = $renderer;
            }
        }
    }

    private function convertCommentViewModel(object $viewModel): object
    {
        $converter = new CommentsViewModelConverter(
            $viewModel,
            $this->data->frameworkUpdates
        );
        return $converter->bakeCommentRenderer();
    }

    public function commentThreadRenderer($context)
    {
        $out = [];

        // PLEASE NOTE:
        // The extra preceding property "comment"/"replies" is removed by this.
        if (isset($context->comment))
        {
            $out['commentRenderer'] = $this->commentRenderer($context->comment->commentRenderer);
        }

        if (isset($context->replies))
        {
            $out['commentRepliesRenderer'] = $this->commentRepliesRenderer($context->replies->commentRepliesRenderer);
        }
        
        return ['commentThreadRenderer' => $out];
    }

    public function commentRenderer(object $context, bool $isReply = false)
    {
        // Right now, the method is to modify a
        // standard InnerTube response.

        $context->isReply = $isReply;
        
        $authorDisplayName = $this->getDisplayName($context->authorEndpoint->browseEndpoint->browseId);

        if (!is_null($authorDisplayName))
        {
            $context->authorText = (object) [
                "simpleText" => $authorDisplayName
            ];
        }

        // Correct mentions
        foreach ($context->contentText->runs as $i => &$run)
        {
            if ($ucid = @$run->navigationEndpoint->browseEndpoint->browseId)
            {
                $mentionDisplayName = $this->getDisplayName($ucid);
                
                /** 
                 * Redo the whole @ string. This also removes the automatic spaces
                 * put around it.
                 */
                if (substr($ucid, 0, 2) == "UC" && !is_null($mentionDisplayName))
                {
                    $run->text = "@" . $mentionDisplayName . "";
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

        // Forced german hack:
        if ($text = ParsingUtils::getText($context->publishedTimeText))
        {
            StringTranslationManager::setText(
                $context->publishedTimeText,
                StringTranslationManager::convertDate($text)
            );
        }
        if ($text = ParsingUtils::getText($context->expandButton->buttonRenderer->text))
        {
            StringTranslationManager::setText(
                $context->expandButton->buttonRenderer->text,
                StringTranslationManager::get($text)
            );
        }
        if ($text = ParsingUtils::getText($context->collapseButton->buttonRenderer->text))
        {
            StringTranslationManager::setText(
                $context->collapseButton->buttonRenderer->text,
                StringTranslationManager::get($text)
            );
        }
        if (
            isset($context->actionButtons->commentActionButtonsRenderer->creatorHeart->creatorHeartRenderer) &&
            $heart = $context->actionButtons->commentActionButtonsRenderer->creatorHeart->creatorHeartRenderer
        )
        {
            if ($text = ParsingUtils::getText($heart->heartedTooltip))
            {
                StringTranslationManager::setText(
                    $heart->heartedTooltip,
                    StringTranslationManager::convertHeart($text)
                );
            }
            if ($text = ParsingUtils::getText($heart->unheartedTooltip))
            {
                StringTranslationManager::setText(
                    $heart->unheartedTooltip,
                    StringTranslationManager::convertHeart($text)
                );
            }
            if ($text = ParsingUtils::getText($heart->heartedAccessibility->accessibilityData))
            {
                StringTranslationManager::setText(
                    $heart->heartedAccessibility->accessibilityData,
                    StringTranslationManager::get($text)
                );
            }
            if ($text = ParsingUtils::getText($heart->unheartedAccessibility->accessibilityData))
            {
                StringTranslationManager::setText(
                    $heart->unheartedAccessibility->accessibilityData,
                    StringTranslationManager::get($text)
                );
            }
        }

        if (
            isset($context->pinnedCommentBadge->pinnedCommentBadgeRenderer) &&
            $text = ParsingUtils::getText($context->pinnedCommentBadge->pinnedCommentBadgeRenderer->label)
        )
        {
            // This is a multi-child runs array, so we need to replace it entirely.
            // Otherwise a result like this may happen:
            //  - Pinned by UsernameUsername angepinnt
            // because just "Von " (first item) is replaced.
            $context->pinnedCommentBadge->pinnedCommentBadgeRenderer->label = (object)[
                "simpleText" => StringTranslationManager::convertPinnedText($text)
            ];
        }

        // comment.linkedCommentBadge.metadataBadgeRenderer.label
        if (isset($context->linkedCommentBadge->metadataBadgeRenderer->label))
        {
            StringTranslationManager::setText(
                $context->linkedCommentBadge->metadataBadgeRenderer->label,
                StringTranslationManager::get(
                    ParsingUtils::getText($context->linkedCommentBadge->metadataBadgeRenderer->label)
                )
            );
        }

        $context->likeButton = MVoteButton::fromData(PropertyAtPath::get($context, self::LIKE_BUTTON_PATH));
        $context->dislikeButton = MVoteButton::fromData(PropertyAtPath::get($context, self::DISLIKE_BUTTON_PATH));
		if (isset($context->voteCount)) $this->addLikeCount($context);
		
        try
        {
            $context->replyButton = MReplyButton::fromData(PropertyAtPath::get($context, self::REPLY_BUTTON_PATH), $context->commentId);
        }
        catch (\YukisCoffee\PropertyAtPathException $e)
        {
            $context->replyButton = null;
        }

        try
        {
            $context->creatorHeart = PropertyAtPath::get($context, self::HEART_BUTTON_PATH);
        }
        catch (\YukisCoffee\PropertyAtPathException $e)
        {
            $context->creatorHeart = null;
        }
        
        // ==== Menu ====
        
        if (isset($context->actionMenu->menuRenderer))
        {
            $nativeMenu = $context->actionMenu->menuRenderer;
            
            $context->actionMenu->rhButtonsSupported = (object)[];
            
            foreach ($nativeMenu->items as $menuItem)
            {
                if (isset($menuItem->menuNavigationItemRenderer->icon->iconType))
                {
                    $item = $menuItem->menuNavigationItemRenderer;
                    $iconType = $menuItem->menuNavigationItemRenderer->icon->iconType;
                    
                    if ($iconType == "FLAG") // Report
                    {
                        $context->actionMenu->rhButtonsSupported->report = $item;
                    }
                    else if ($iconType == "DELETE")
                    {
                        $context->actionMenu->rhButtonsSupported->delete = $item;
                    }
                    else if ($iconType == "EDIT")
                    {
                        $context->actionMenu->rhButtonsSupported->edit = $item;
                        
                        if (isset($item->navigationEndpoint->updateCommentDialogEndpoint))
                        {
                            $item->rhEditDialog = $item->navigationEndpoint->updateCommentDialogEndpoint
                                ->dialog->commentDialogRenderer;
                        }
                        else if (isset($item->navigationEndpoint->updateCommentReplyDialogEndpoint))
                        {
                            $item->rhEditDialog = $item->navigationEndpoint->updateCommentReplyDialogEndpoint
                                ->dialog->commentReplyDialogRenderer;
                        }
                        
                        if (isset($item->rhEditDialog))
                        {
                            if (isset($item->rhEditDialog->submitButton))
                            {
                                $item->rhEditParams = $item->rhEditDialog->submitButton
                                    ->buttonRenderer->serviceEndpoint->updateCommentEndpoint
                                    ->updateCommentParams;
                            }
                            else if (isset($item->rhEditDialog->replyButton))
                            {
                                $item->rhEditParams = $item->rhEditDialog->replyButton
                                    ->buttonRenderer->serviceEndpoint->updateCommentReplyEndpoint
                                    ->updateReplyParams;
                            }
                        }
                    }
                    else if ($iconType == "KEEP") // Pin
                    {
                        $context->actionMenu->rhButtonsSupported->pin = $item;
                    }
                    else if ($iconType == "BLOCK")
                    {
                        $context->actionMenu->rhButtonsSupported->block = $item;
                    }
                }
            }
        }

        return $context;
    }

    // WHAT THE FUCK
    public function commentRepliesRenderer($context)
    {
        if (isset($context->viewReplies))
        {
            $i18n = i18n::getNamespace("comments");

            $viewText = &$context->viewReplies->buttonRenderer->text->runs[0]->text;
            $hideText = &$context->hideReplies->buttonRenderer->text->runs[0]->text;

            // Restore the old reply count text used before 2022.
            // "View {count} replies" instead of "{count} REPLIES"
            // "View {count} reply from {author}" instead of "author pfp・1 REPLY"
            // etc.
            $replyCount = (int) preg_replace($i18n->get("replyCountIsolator"), "", $viewText);
            if (isset($context->viewRepliesCreatorThumbnail))
            {
                $creatorName = $context->viewRepliesCreatorThumbnail->accessibility->accessibilityData->label;
            }

            if ($replyCount > 1)
            {
                if (isset($creatorName))
                {
                    $viewText = $i18n->format("viewMultiOwner", $replyCount, $creatorName);
                }
                else
                {
                    $viewText = $i18n->format("viewMulti", $replyCount);
                }
            }
            else
            {
                if (isset($creatorName))
                {
                    $viewText = $i18n->format("viewSingularOwner", $creatorName);
                }
                else
                {
                    $viewText = $i18n->get("viewSingular");
                }
            }

            $hideText = ($replyCount > 1)
                ? $i18n->format("hideMulti", $replyCount)
                : $i18n->get("hideSingular");
        }

        /*
         * Process teaser contents.
         */
        if (isset($context->teaserContents)) foreach($context->teaserContents as $item)
        {
            if (isset($item->commentRenderer))
                $item->commentRenderer = $this->commentRenderer($item->commentRenderer, true);
        }
        
        /*
         * Make the comment use our custom token.
         * 
         * The custom token is used to maintain display names between comment continuations.
         */
        if (isset($context->contents[0]->continuationItemRenderer->continuationEndpoint->continuationCommand))
        {
            $continuationCommand = $context->contents[0]->continuationItemRenderer
                ->continuationEndpoint->continuationCommand;
            
            $rhToken = new CommentsContinuation($continuationCommand->token);
            $rhToken->supplyDisplayNameMap($this->createDisplayNameMap());
            
            $continuationCommand->token = $rhToken;
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
        
        $rhToken = new CommentsContinuation($context->command->continuationCommand->token);
        $rhToken->supplyDisplayNameMap($this->createDisplayNameMap());
        
        return
            [
                "token" => $rhToken,
                "text" => StringTranslationManager::get(ParsingUtils::getText($context->text))
            ];
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

        $context->isLiked = $context->isLiked ?? @$context->likeButton->checked;
		if ($context->isLiked)
        {
			$context->voteCount = [
				"indifferentText" => (string)($count - 1),
				"likedText" => (string)$count
			];
		}
        else
        {
			$context->voteCount = [
				"indifferentText" => (string)$count,
				"likedText" => (string)($count + 1)
			];
		}
    }

    private function getLikeCountFromLabel($label)
    {
        // return preg_replace("/[^0-9]/", "", $label);
        return StringTranslationManager::convertLikeCount($label);
    }
}