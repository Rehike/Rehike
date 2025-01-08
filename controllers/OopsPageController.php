<?php
namespace Rehike\Controller;

use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for the oops (error) page.
 * 
 * Very simple one, I know. All it's needed for is making a bridge between
 * CV2 and the static error page.
 * 
 * @author The Rehike Maintainers
 */
class OopsPageController extends HitchhikerController implements IGetController
{
    public string $template = "oops";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $this->setTitle("Oops! Something went wrong.");
    }
}