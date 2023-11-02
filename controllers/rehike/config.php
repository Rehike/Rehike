<?php
namespace Rehike\Controller\rehike;

use Rehike\Model\Rehike\Config\ConfigModel;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

return new class extends \Rehike\Controller\core\NirvanaController
{
    public string $template = "rehike/config/main";

    const DEFAULT_TAB = "appearance";

    const VALID_TABS = [
        "appearance",
        "experiments",
        "advanced"
    ];

    /**
     * Configures view properties for the page.
     */
    public function setupViewProps(\Rehike\ViewProperties $vp): void
    {
        $vp->pageClassName = "search";
        $vp->guideDefaultVisibility = false;
        $vp->appbarDefaultVisibility = false;
        $vp->enableSnapScaling = true;
        $vp->flexWidthSnapDisabled = true;
    }

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $tab = $request->path[2] ?? self::DEFAULT_TAB;

        if (!in_array($tab, self::VALID_TABS))
        {
            header("Location: /rehike/config/" . self::DEFAULT_TAB);
        }

        $yt->page = ConfigModel::bake($tab, $request->params->status ?? null);
    }
};