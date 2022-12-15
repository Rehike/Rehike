<?php
use \Rehike\Controller\core\HitchhikerController;

/**
 * Controller for the live chat iframe's page.
 * 
 * TODO (kirasicecreamm) This is currently very unfinished.
 * 
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
    public $template = "live_chat";

    function onGet(&$yt, $request) {
        $yt->useModularCore = true;
    }
};