<?php
namespace Rehike\Controller\core;

use Rehike\YtApp;
use Rehike\Spf\Spf;

use Rehike\Model\{
    Appbar\MAppbar,
    Footer\MFooter,
    Masthead\MMasthead
};

/**
 * Defines a general YouTube Nirvana controller.
 * 
 * This implements the base API and data used to render a Nirvana (Appbar)
 * page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
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
        $yt->appbar = new MAppbar();
        $yt->page = (object)[];

        if ($this->useTemplate)
        {
            $yt->masthead = new MMasthead(true);
            $yt->footer = new MFooter();
        }
        $yt->footer = new MFooter();

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