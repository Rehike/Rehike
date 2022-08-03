<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;
use \Rehike\Model\Picker\PickerModel;

return new class extends AjaxController {
    public $template = "ajax/picker";

    public function onGet(&$yt, $request) {
        $action = self::findAction();

        $response = Request::innertubeRequest("account/account_menu", (object) [
            "deviceTheme" => "DEVICE_THEME_SUPPORTED",
            "userInterfaceTheme" => "USER_INTERFACE_THEME_LIGHT"
        ]);
        $ytdata = json_decode($response);

        $yt -> page = PickerModel::bake($ytdata, $action);
    }
};