<?php
namespace Rehike\Controller\rehike;

use Rehike\YtApp;
use Rehike\ControllerV2\RequestMetadata;

use Rehike\ConfigManager\Config;

return new class extends \Rehike\Controller\core\AjaxController
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
};