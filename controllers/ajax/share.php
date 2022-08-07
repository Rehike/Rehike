<?php
use \Rehike\Controller\core\AjaxController;

return new class extends AjaxController {
    public function onGet(&$yt, $request) {
        $action = self::findAction();

        if (!@$action) {
            http_response_code(400);
            echo json_encode((object) [
                "errors" => []
            ]);
        }

        switch ($action) {
            case "get_share_box":
                self::getShareBox($yt, $request);
                break;
        }
    }

    /**
     * Get the share box.
     */
    private function getShareBox(&$yt, $request) {
        $this -> template = "ajax/share/get_share_box";
    }
};