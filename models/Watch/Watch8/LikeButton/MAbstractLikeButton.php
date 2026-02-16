<?php
namespace Rehike\Model\Watch\Watch8\LikeButton;

use Rehike\Model\Common\MToggleButton;
use Rehike\i18n\i18n;

/**
 * Define an abstract actual "like button" button (also used for dislikes).
 */
class MAbstractLikeButton extends MToggleButton
{
    protected $hideNotToggled = true;

    public string $style = "opacity";
    
    /**
     * @inheritDoc
     */
    public array $attributes = [
        "orientation" => "vertical",
        "position" => "bottomright",
        "force-position" => "true"
    ];

    public function __construct($type, $active, $count, $state)
    {
        parent::__construct($state);

        $this->icon = (object) [];

        $class = "like-button-renderer-" . $type;
        $this->class[] = $class;
        $this->class[] = $class . "-" . ($active ? "clicked" : "unclicked");
        if ($active)
            $this->class[] = "yt-uix-button-toggled";

        if (!is_null($count))
            $this->setText(i18n::formatNumber($count));
    }
}