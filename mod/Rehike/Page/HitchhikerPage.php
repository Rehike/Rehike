<?php
namespace Rehike\Page;

class HitchhikerPage extends AbstractPage
{
    public $spfEnabled = false; // Nirvana-only due to appbar
    public $useModularCore = false;
    public $modularCoreModules = [];
    public $response;

    public function pushJsModule($module)
    {
        $this->modularCoreModules[] = $module;
    }

    public function pushJsModules($modules)
    {
        $this->modularCoreModules += $modules;
    }
}