<?php
namespace Rehike\Model\Comments;

use Rehike\TemplateFunctions;

class CommentsHeader {
    public $title;
    public $commentsCountText;
    public $sortRenderer;
    public $simpleBoxRenderer;

    public static function fromData($data) {
        $new = new self();

        if ($a = @$data->titleText) {
            $new->title = TemplateFunctions::getText($data->titleText);
        }
        
        if ($a = @$data->countText) {
            $a = $a->runs;
            $new->commentsCountText = $a[0]->text;
        }

        if ($a = @$data->createRenderer) {
            $new->createParams = $a->commentSimpleboxRenderer->submitButton->buttonRenderer->serviceEndpoint->createCommentEndpoint->createCommentParams ?? null;
        }

        if ($a = $data->sortMenu) {
            $a = $a->sortFilterSubMenuRenderer->subMenuItems; // everything we need in here...
            $new->sortRenderer = (object) [];
            $_sr = $new->sortRenderer; // shorthand
            for ($i = 0; $i < count($a); $i++) {
                $item = $a[$i];
        
                if ($item->selected) {
                    $_sr->title = $item->title;
                }
        
                $_sr->items[$i] = (object) [];
                $_sri = $_sr->items[$i]; // shorthand
                
                $_sri->title = $item->title;
                $_sri->selected = $item->selected;
                $_sri->continuation = $item->serviceEndpoint->continuationCommand->token;
        
                // just in case, probably won't do much harm
                $_sri->menuName = (function() use ($i){
                    switch($i) {
                        case 0: return 'top-comments';
                        case 1: return 'newest-first';
                    }
                })();
            }
        }

        if ($a = ($data->createRenderer->commentSimpleboxRenderer ?? false)) {
            $new->simpleBoxRenderer = (object) [];
            $_sbr = $new->simpleBoxRenderer; // shorthand
            $_sbr->authorThumbnail = $a->authorThumbnail;
            $_sbr->placeholderText = TemplateFunctions::getText($a->placeholderText);
        }

        return $new;
    }
}