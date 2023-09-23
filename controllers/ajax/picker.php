<?php
namespace Rehike\Controller\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use \Rehike\Controller\core\AjaxController;
use \Rehike\Network;
use \Rehike\Model\Picker\PickerModel;
use \Rehike\Signin\API as SignIn;
use \Rehike\Signin\AuthManager;
use \Rehike\Signin\Cacher;

/**
 * Controller for the account picker AJAX endpoint.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
return new class extends AjaxController {
    public string $template = "ajax/picker";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $action = self::findAction();

        Network::innertubeRequest(
            action: "account/account_menu",
            body: [
                "deviceTheme" => "DEVICE_THEME_SUPPORTED",
                "userInterfaceTheme" => "USER_INTERFACE_THEME_LIGHT"
            ]
        )->then(function ($response) use ($yt, $action) {
            $ytdata = $response->getJson();

            $yt->page = PickerModel::bake($ytdata, $action);
        });
    }
};