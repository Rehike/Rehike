<?php
use \Rehike\Controller\core\HitchhikerController;

/**
 * Controller for the oops (error) page.
 * 
 * Very simple one, I know. All it's needed for is making a bridge between
 * CV2 and the static error page.
 * 
 * @author The Rehike Maintainers
 */
return new class extends HitchhikerController {
    public string $template = "oops";
};