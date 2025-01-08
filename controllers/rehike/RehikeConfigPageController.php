<?php
namespace Rehike\Controller\rehike;

use Rehike\Controller\core\NirvanaController;

use Rehike\Model\Rehike\Config\ConfigModel;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for the Rehike configuration page.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeConfigPageController extends NirvanaController implements IGetController
{
    public string $template = "rehike/config/main";

    const DEFAULT_TAB = "appearance";

    const VALID_TABS = [
        "appearance",
        "experiments",
        "advanced"
    ];

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $tab = $request->path[2] ?? self::DEFAULT_TAB;

        if (!in_array($tab, self::VALID_TABS))
        {
            header("Location: /rehike/config/" . self::DEFAULT_TAB);
        }

        $yt->page = ConfigModel::bake($tab, $request->params->status ?? null);
    }
}