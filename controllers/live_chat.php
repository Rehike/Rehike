<?php
use \Rehike\Controller\core\HitchhikerController;

return new class extends HitchhikerController {
    public $template = "live_chat";

    function onGet(&$yt, $request) {
        $yt -> useModularCore = true;
    }
};