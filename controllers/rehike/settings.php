<?php
use \Rehike\Controller\core\NirvanaController;
use \Rehike\Model\Rehike\Settings\SettingsModel;

return new class extends NirvanaController {
    public $template = "rehike/settings";

    public function onGet(&$yt, $request) {
        $yt -> page = SettingsModel::bake();
    }
};