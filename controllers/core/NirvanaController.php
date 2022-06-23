<?php
namespace Rehike\Controller\core;

abstract class NirvanaController extends HitchhikerController
{
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

    protected function init(&$yt, &$template)
    {
        $yt->spfEnabled = true;
        $yt->useModularCore = true;
        $yt->modularCoreModules = [];
        $yt->page = (object)[];

        // TODO: Guide fragments should be requested here ideally.
        // As guide gets restructured, this behaviour should be
        // implemented.
    }

    protected function useJsModule($module)
    {
        $this->yt->modularCoreModules[] = $module;
    }
}