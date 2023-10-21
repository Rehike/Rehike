<?php
namespace Rehike\Model\Channels\Channels4\Sidebar;

use Rehike\i18n\i18n;

class MRelatedChannelsSeeMoreButton
{
    public $title;
    public $href;

    public function __construct($href)
    {
        $strings = i18n::getNamespace("channels");

        $this->title = $strings->get("seeAll");
        $this->href = $href;
    }
}