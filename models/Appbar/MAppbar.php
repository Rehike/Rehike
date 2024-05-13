<?php
namespace Rehike\Model\Appbar;

use Rehike\i18n\i18n;

use Rehike\Model\Guide\MGuide;

/**
 * Model for the appbar on Nirvana pages.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MAppbar
{
    public MAppbarNav $nav;
    public MGuide $guide;
    public object $guideNotificationStrings;

    /**
     * Add a centered navigation section to the appbar.
     */
    public function addNav(): void
    {
        $this->nav = new MAppbarNav();
    }

    /**
     * Add a guide response to the appbar.
     */
    public function addGuide(MGuide $guide): void
    {
        $this->guide = $guide;
    }
}