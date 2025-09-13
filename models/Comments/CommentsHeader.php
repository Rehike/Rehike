<?php
namespace Rehike\Model\Comments;

use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\Util\FormattedStringBuilder;
use Rehike\Util\FormattedStringBuilder\PrintfTemplateBuilderParams;
use Rehike\i18n\Internal\Core as I18nCore;

/**
 * Model for the comments header on the watch page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class CommentsHeader
{
    public ?string $title;
    public ?string $commentsCountText;
    public ?object $sortRenderer;
    public ?object $simpleBoxRenderer;
    public ?object $pausedCommentsMessage = null;
    public ?string $createParams;
    public ?string $commentText;

    public static function fromData($data)
    {
        $new = new self();

        if ($a = @$data->titleText)
        {
            $new->title = StringTranslationManager::get(
                ParsingUtils::getText($data->titleText)
            );
        }
        
        if ($a = @$data->countText)
        {
            $a = $a->runs;
            $new->commentsCountText = StringTranslationManager::reformatNumber($a[0]->text);
        }

        if ($a = @$data->createRenderer)
        {
            $new->createParams = $a->commentSimpleboxRenderer->submitButton->buttonRenderer->serviceEndpoint->createCommentEndpoint->createCommentParams ?? null;
            $new->commentText = StringTranslationManager::get(
                ParsingUtils::getText($a->commentSimpleboxRenderer->submitButton->buttonRenderer->text)
            );
        }

        if ($a = $data->sortMenu)
        {
            $a = $a->sortFilterSubMenuRenderer->subMenuItems; // everything we need in here...
            $new->sortRenderer = (object) [];
            $_sr = $new->sortRenderer; // shorthand
            for ($i = 0; $i < count($a); $i++)
            {
                $item = $a[$i];
        
                if ($item->selected)
                {
                    $_sr->title = StringTranslationManager::get(ParsingUtils::getText($item->title));
                }
        
                $_sr->items[$i] = (object) [];
                $_sri = $_sr->items[$i]; // shorthand
                
                $_sri->title = StringTranslationManager::get(ParsingUtils::getText($item->title));
                $_sri->selected = $item->selected;
                $_sri->continuation = $item->serviceEndpoint->continuationCommand->token;
        
                // just in case, probably won't do much harm
                $_sri->menuName = (function() use ($i){
                    switch($i)
                    {
                        case 0: return 'top-comments';
                        case 1: return 'newest-first';
                    }
                })();
            }
        }

        if ($a = ($data->createRenderer->commentSimpleboxRenderer ?? false))
        {
            $new->simpleBoxRenderer = (object) [];
            $_sbr = $new->simpleBoxRenderer; // shorthand
            $_sbr->authorThumbnail = $a->authorThumbnail;
            $_sbr->placeholderText = StringTranslationManager::get(
                ParsingUtils::getText($a->placeholderText)
            );
        }
        else if ($a = ($data->pausedCommentsMessage))
        {
            $i18n = i18n::getNamespace("comments");
            $hl = I18nCore::getInnertubeLanguageId();
            
            $new->pausedCommentsMessage = (object)[
                "messageRenderer" => (object)[
                    "text" => (new FormattedStringBuilder())->parseFromPrintfTemplates(
                        new PrintfTemplateBuilderParams($i18n->get("commentsPaused")),
                        new PrintfTemplateBuilderParams($i18n->get("commentsPausedLearnMore"),
                            FormattedStringBuilder::RUN_AS_LINK, 
                            "https://support.google.com/youtube/?p=pause_comments&hl=$hl"
                        )
                    )->build(),
                ],
            ];
        }

        return $new;
    }
}