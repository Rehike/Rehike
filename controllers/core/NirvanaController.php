<?php
namespace Rehike\Controller\core;

use Rehike\YtApp;
use Rehike\Spf\Spf;

use Rehike\Model\{
    Appbar\MAppbar as Appbar,
    Footer\MFooter as Footer,
    Masthead\MMasthead as Masthead
};

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
     */
    protected bool $delayLoadGuide = false;

    /** @inheritdoc */
    protected function init(YtApp $yt, string &$template): void
    {
        $yt->spfEnabled = true;
        $yt->useModularCore = true;
        $yt->modularCoreModules = [];
        $yt->appbar = new Appbar();
        $yt->page = (object)[];

        if ($this->useTemplate)
        {
            $yt->masthead = new Masthead(true);
            $yt->footer = new Footer();
        }
        $yt->footer = new Footer();

        // Request appbar guide fragments if the page has the
        // guide enabled, the request is not SPF, and the guide
        // is open by default.
        if (!$this->delayLoadGuide && !Spf::isSpfRequested())
        {
            $this->getPageGuide()->then(function ($guide) use ($yt) {
                $yt->appbar->addGuide($guide);
            });
        }
    }

    /**
     * Define the page to use a JS page module.
     * 
     * @param string $module  Name of the module (not URL)
     * 
     * @return void
     */
    protected function useJsModule(string $module): void
    {
        $this->yt->modularCoreModules[] = $module;
    }
}