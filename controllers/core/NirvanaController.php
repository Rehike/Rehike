<?php
namespace Rehike\Controller\core;

use SpfPhp\SpfPhp;
use Rehike\Model\Appbar\MAppbar as Appbar;
use Rehike\Model\Footer\MFooter as Footer;
use Rehike\Model\Masthead\MMasthead as Masthead;

/**
 * Defines a general YouTube Nirvana controller.
 * 
 * This implements the base API and data used to render a Nirvana (Appbar)
 * page.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 */
abstract class NirvanaController extends HitchhikerController
{
    /**
     * Don't request the guide on initial visit.
     * 
     * This should be true on pages like watch, where the guide
     * isn't open by default.
     * 
     * @var bool
     */
    protected $delayLoadGuide = false;

    /** @inheritdoc */
    protected $spfIdListeners = [
        '@body<class>',
        'player-unavailable<class>',
        'debug',
        'early-body',
        'appbar-content<class>',
        'alerts',
        'content',
        '@page<class>',
        'header',
        'ticker-content',
        'player-playlist<class>',
        '@player<class>'
    ];

    /** @inheritdoc */
    protected function init(&$yt, &$template)
    {
        $yt->spfEnabled = true;
        $yt->useModularCore = true;
        $yt->modularCoreModules = [];
        $yt->appbar = new Appbar();
        $yt->page = (object)[];

        if ($this -> useTemplate) {
            $yt -> masthead = new Masthead(true);
            $yt -> footer = new Footer();
        }
        $yt -> footer = new Footer();

        // Request appbar guide fragments if the page has the
        // guide enabled, the request is not SPF, and the guide
        // is open by default.
        if (!$this->delayLoadGuide && !SpfPhp::isSpfRequested())
        {
            //$yt->appbar->addGuide($this->getPageGuide());

            // Attempt async (better way):
            $this->getGuideAsync();
        }
    }

    public function postInit(&$yt, &$template)
    {
        parent::postInit($yt, $template);

        // Load guide result (if available)
        if ($this->hasAsyncGuideRequest())
        {
            $yt->appbar->addGuide(
                $this->getGuideAsyncResult()
            );
        }
    }

    /**
     * Define the page to use a JS page module.
     * 
     * @param string $module  Name of the module (not URL)
     * 
     * @return void
     */
    protected function useJsModule($module)
    {
        $this->yt->modularCoreModules[] = $module;
    }
}