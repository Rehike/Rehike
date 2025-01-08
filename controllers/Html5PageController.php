<?php
namespace Rehike\Controller;

use \Rehike\Controller\core\HitchhikerController;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\YtApp;

use Rehike\ControllerV2\{
    IGetController,
    IPostController,
};

/**
 * Controller for the HTML5 video support page.
 * 
 * Even simpler than the oops page
 * 
 * @author Toru the Red Fox
 * @author The Rehike Maintainers
 */
class Html5PageController extends HitchhikerController implements IGetController
{
    public string $template = "html5";
    
    // onGet inherited from HitchhikerController
}