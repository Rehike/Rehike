<?php
namespace Rehike\Model\Comments;
use \Rehike\Model\Common\MButton;

class MCommentVoteButton extends MButton {
    // from MButton
    public $hasIcon = true;
    public $noIconMarkup = true;
    public $class = [
        "comment-action-buttons-renderer-thumb",
        "sprite-comment-actions"
    ];
    public $attributes = [
        "url" => "/comment_service_ajax?action_perform_comment_action=1"
    ];

    /** @var string */
    public $type;

    /** @var string */
    public $action;

    /** @var string */
    public $a11yLabel;

    /** @var boolean */
    public $checked;

    public function __construct($data) {
        $this -> type = $data["type"];
        $this -> action = $data["action"] ?? "";
        $this -> a11yLabel = $data["a11yLabel"] ?? null;
        $this -> checked = $data["checked"] ?? false;

        $this -> class[] = "sprite-" . $this -> type;
        $this -> class[] = "i-a-v-sprite-" . $this -> type;

        $this -> attributes["action-type"] = $this -> type;
        $this -> attributes["action"] = $this -> action;
        $this -> accessibilityAttributes["checked"] = $this -> checked ? "true" : false;
    }

    public static function fromData($data) {
        $type = strtolower(@$data -> defaultIcon -> iconType) ?? null;
        $checked = $data -> isToggled ?? false;
        $action = $checked ? $data -> toggledServiceEndpoint -> performCommentActionEndpoint -> action : $data -> defaultServiceEndpoint -> performCommentActionEndpoint -> action ?? null;
        $a11yLabel = $checked ? $data -> toggledTooltip : $data -> defaultTooltip ?? null;

        return new self([
            "type" => $type,
            "action" => $action,
            "a11yLabel" => $a11yLabel,
            "checked" => $checked
        ]);
    }
}