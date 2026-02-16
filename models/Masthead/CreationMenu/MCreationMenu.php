<?php
namespace Rehike\Model\Masthead\CreationMenu;

use Rehike\Model\Common\MButton;

class MCreationMenu extends MButton
{
    public string $targetId = "yt-masthead-creation-button";
    
    /**
     * @inheritDoc
     */
    public array $attributes = [
        "force-position" => "true",
        "position-fixed" => "true",
        "orientation" => "vertical",
        "position" => "bottomleft"
    ];

    public function __construct()
    {
        $this->clickcard = new MCreationClickcard();
        $this->icon = (object) [];
    }
}