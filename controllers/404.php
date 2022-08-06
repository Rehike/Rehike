<?php
use \Rehike\Controller\core\HitchhikerController;

return new class extends HitchhikerController {
    public $template = "error/404";

    public function onGet(&$yt, $request) {
        http_response_code(404);
    }
};