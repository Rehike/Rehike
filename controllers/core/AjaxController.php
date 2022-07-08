<?php
namespace Rehike\Controller\core;

use \Rehike\Controller\core\NirvanaController;

abstract class AjaxController extends NirvanaController {
    // Find action
    // Not used for watch_fragments or watch_fragments2 (electric boogaloo)
    protected function findAction() {
        foreach ($_GET as $key => $value) {
            if (strpos($key, "action_") > -1) {
                return str_replace("action_", "", $key);
            }
        }
        return null;
    }
}