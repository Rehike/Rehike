<?php
namespace Rehike\Controller\rehike;

use Rehike\Model\Rehike\Extensions\ExtensionsPageModel;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

return new class extends \Rehike\Controller\core\NirvanaController
{
    public string $template = "rehike/extensions_page/main";

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $yt->page = new ExtensionsPageModel();
    }
};