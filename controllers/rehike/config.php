<?php
use Rehike\Model\Rehike\Config\ConfigModel;

return new class extends \Rehike\Controller\core\NirvanaController {
    public $template = "rehike/config/main";

    const DEFAULT_TAB = "appearance";

    const VALID_TABS = [
        "appearance",
        "advanced"
    ];

    public function onGet(&$yt, $request) {
        $tab = $request -> path[2] ?? self::DEFAULT_TAB;

        if (!in_array($tab, self::VALID_TABS)) {
            header("Location: /rehike/config/" . self::DEFAULT_TAB);
        }

        $yt -> page = ConfigModel::bake($tab, $request -> params -> status ?? null);
    }
};