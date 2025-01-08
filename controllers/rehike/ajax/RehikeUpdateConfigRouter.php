<?php
namespace Rehike\Controller\rehike\ajax;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\ConfigManager\Config;

use Rehike\Controller\core\AjaxController;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for /rehike/update_config AJAX endpoint.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeUpdateConfigRouter extends AjaxController implements IPostController
{
    public bool $useTemplate = false;

    public function onPost(YtApp $yt, RequestMetadata $request): void
    {
        $input = \json_decode(\file_get_contents('php://input'), true);

        try
        {
            foreach ($input as $option => $value)
            {
                Config::setConfigProp(
                    $option,
                    $value
                );
            }
            Config::dumpConfig();
        }
        catch(\Throwable $e)
        {
            http_response_code(400);
            echo $e;
        }
    }
}