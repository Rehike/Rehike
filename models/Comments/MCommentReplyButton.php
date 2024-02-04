<?php
namespace Rehike\Model\Comments;
use \Rehike\Model\Common\MButton;
use \Rehike\Util\ParsingUtils;

class MCommentReplyButton extends MButton {
    public $size = "SIZE_SMALL";
    public $style = "STYLE_LINK";
    public $class = [
        "comment-renderer-reply",
        "comment-simplebox-trigger",  
    ];
    public $attributes = [
        "simplebox-target" => "/comment_service_ajax?action_create_comment_reply=1"
    ];

    public function __construct($data) {
        $this->attributes["simplebox-id"] = "comment-simplebox-reply-" . $data["id"];
        $this->attributes["simplebox-params"] = $data["params"] ?? null;
        $this->attributes["simplebox-label"] = $data["label"] ?? "";
        $this->attributes["placeholder"] = $data["placeholder"] ?? "";
        $this->text = $data["text"];
    }

    public static function fromData($data, $id) {
        $dialog = $data->navigationEndpoint->createCommentReplyDialogEndpoint->dialog->commentReplyDialogRenderer ?? null;
        $params = $dialog->replyButton->buttonRenderer->serviceEndpoint->createCommentReplyEndpoint->createReplyParams ?? "";
        $label = StringTranslationManager::get(ParsingUtils::getText($dialog->replyButton->buttonRenderer->text));
        $placeholder = StringTranslationManager::get(ParsingUtils::getText($dialog->placeholderText));
        $text = $data->text;
        // Need to do this or it breaks:
        StringTranslationManager::setText(
            $text,
            StringTranslationManager::get(ParsingUtils::getText($text))
        );
        return new self([
            "id" => $id,
            "params" => $params,
            "label" => $label,
            "placeholder" => $placeholder,
            "text" => $text
        ]);
    }
}