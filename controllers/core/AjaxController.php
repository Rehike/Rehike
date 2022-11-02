<?php
namespace Rehike\Controller\core;

use \Rehike\Controller\core\NirvanaController;

/**
 * Defines a general AJAX endpoint controller.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class AjaxController extends NirvanaController {
    public $contentType = "application/json";

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

    protected static function error() {
        http_response_code(400);
        die('{"errors":[]}');
    }
}