<?php
namespace Rehike\Controller\rehike;

use Rehike\ControllerV2\BaseController;
use Rehike\ControllerV2\IGetController;
use Rehike\ControllerV2\IPostController;
use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

/**
 * Simple server info controller stub.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeServerInfoPageController extends BaseController implements IGetController, IPostController
{
    public function get(): void
    {
        phpinfo();
    }

    public function post(): void
    {
        $this->get();
    }
}