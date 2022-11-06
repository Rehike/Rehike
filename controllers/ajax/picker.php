<?php
use \Rehike\Controller\core\AjaxController;
use \Rehike\Request;
use \Rehike\Model\Picker\PickerModel;
use \Rehike\Signin\API as SignIn;
use \Rehike\Signin\AuthManager;
use \Rehike\Signin\Cacher;

return new class extends AjaxController {
    public $template = "ajax/picker";

    public function onGet(&$yt, $request) {
        $action = self::findAction();

        // Get cache if it exists
        if (SignIn::isSignedIn() && $cache = Cacher::getCache()) {
            $id = AuthManager::getUniqueSessionCookie();

            if ($menu = @$cache -> responseCache -> {$id} -> menu) {
                $ytdata = $menu;
            }
        }

        // Perform request like normal
        if (!isset($ytdata)) {
            $response = Request::innertubeRequest("account/account_menu", (object) [
                "deviceTheme" => "DEVICE_THEME_SUPPORTED",
                "userInterfaceTheme" => "USER_INTERFACE_THEME_LIGHT"
            ]);
            $ytdata = json_decode($response);
        }

        $yt -> page = PickerModel::bake($ytdata, $action);
    }
};