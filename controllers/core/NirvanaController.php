<?php
namespace Rehike\Controller\core;

use Rehike\Model\Appbar\MAppbar as Appbar;

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

        // TODO: Guide fragments should be requested here ideally.
        // As guide gets restructured, this behaviour should be
        // implemented.
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