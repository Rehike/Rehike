<?php
use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

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

    public function onGet(YtApp $yt, RequestMetadata $request): void
    {
        $this->setTitle("Oops! Something went wrong.");
    }
};