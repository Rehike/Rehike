<?php
namespace Rehike\Page;

class HitchhikerPage extends AbstractPage
{
    public $spfEnabled = false; // Nirvana-only due to appbar
    public $useModularCore = false;
    public $modularCoreModules = [];
    public $spfIdListeners = 
        [
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
            'player-playlist',
            '@player<class>'
        ];
    public $response;

    protected function pushJsModule($module)
    {
        $this->modularCoreModules[] = $module;
    }

    protected function pushJsModules($modules)
    {
        $this->modularCoreModules += $modules;
    }
}