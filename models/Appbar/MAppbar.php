<?php
namespace Rehike\Model\Appbar;

use Rehike\i18n;

use Rehike\Model\Guide\MGuide;

class MAppbar
{
    public $nav;
    public $guide;
    public $guideNotificationStrings;

    /**
     * Add a centre navigation section to the appbar.
     * 
     * @return void
     */
    public function addNav()
    {
        $this->nav = new MAppbarNav();
    }

    /**
     * Add a guide response to the appbar.
     * 
     * @param MGuide $guide
     * @return void
     */
    public function addGuide($guide)
    {
        $this->guide = $guide;

        $this->guideNotificationStrings =
            i18n::getNamespace("main/guide")->notifications
        ;
    }
}