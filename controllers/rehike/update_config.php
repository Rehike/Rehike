<?php
use Rehike\RehikeConfigManager as ConfigManager;

return new class extends \Rehike\Controller\core\AjaxController {
    public $useTemplate = false;

    public function onPost(&$yt, $request) {
        $input = \json_decode(\file_get_contents('php://input'), true);

        try {
            foreach ($input as $option => $value) {
                ConfigManager::setConfigProp(
                    $option,
                    $value
                );
            }
            ConfigManager::dumpConfig();
        } catch(Throwable $e) {
            http_response_code(400);
        }
    }
};