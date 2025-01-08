<?php
namespace Rehike\Controller\rehike;

use Rehike\Model\Rehike\Extensions\ExtensionsPageModel;

use Rehike\Controller\core\NirvanaController;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for the Rehike extensions page, which has yet to be implemented.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeExtensionsPageController extends NirvanaController implements IGetController
{
    public string $template = "rehike/extensions_page/main";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $yt->page = new ExtensionsPageModel();
    }
}