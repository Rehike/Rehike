<?php
use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

/**
 * Controller for the HTML5 video support page.
 * 
 * Even simpler than the oops page
 * 
 * @author Toru the Red Fox
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
    public string $template = "html5";
};