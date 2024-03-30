<?php
namespace Rehike\Model\Share;

use Rehike\Model\Common\MButton;

/**
 * Model for the page tab bar of the share box.
 * 
 * @author syndiate
 * @author The Rehike Maintainers
 */
class MShareTabBar
{
    /** @var MButton[] */
    public $tabs = [];

    public function __construct($tabs)
    {
        for ($i = 0; $i < count($tabs); $i++)
        {
            $this->tabs[] = new MButton([
                "style" => "STYLE_TEXT",
                "text" => (object) [
                    "simpleText" => $tabs[$i] ->text ?? ""
                ],
                "class" => [
                    "share-panel-" . $tabs[$i] ->type,
                    "yt-card-title",
                    $tabs[$i] ->active ? "yt-uix-button-toggled" : ""
                ],
                "attributes" => [
                    "button-toggle" => "true"
                ]
            ]);
        }
    }
}