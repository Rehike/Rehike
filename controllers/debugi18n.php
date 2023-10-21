<?php

use Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\i18n\i18n;
use Rehike\YtApp;

return new class extends HitchhikerController
{
    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $yt->page = i18n::getAllTemplates("rehike/config");
    }
};