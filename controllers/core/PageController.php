<?php
namespace Rehike\Controller\core;

use Rehike\ControllerV2\IController;

use Rehike\ControllerV2\BaseController;
use Rehike\YtApp;

/**
 * 
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PageController extends BaseController
{
    /**
     * Stores all information that is sent to Twig for rendering the page.
     * 
     * @var YtApp $yt
     *   + useModularCore (bool, required) - Toggles base.js/core.js use by Hitchhiker.
     *   + modularCoreModules (string[]) - Defines base.js page modules.
     *   + spfEnabled (bool, required) - Enables YouTube SPF (soft loading).
     *   + spf (bool, required) - True if the page is navigated to via SPF.
     *   + title (string) - Page title name
     *   + appbar (object) - Available in NirvanaController; defines YouTube Appbar.
     *   + page (object) - Page metadata
     */
    protected YtApp $yt;

    /**
     * Defines the default page template.
     * 
     * This may be overridden for certain contexts in an onGet()
     * callback.
     */
    public string $template = "";
    
    public function getYtApp(): YtApp
    {
        return $this->yt;
    }
    
    public function getTemplate(): string
    {
        return $this->template;
    }
    
    public function setTemplate(string $newTemplate): void
    {
        $this->template = $newTemplate;
    }
}