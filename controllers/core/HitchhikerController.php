<?php
namespace Rehike\Controller\core;

use Rehike\TemplateManager;
use SpfPhp\SpfPhp;
use Rehike\ControllerV2\RequestMetadata;

/**
 * Defines a general YouTube Hitchhiker controller.
 * 
 * This implements the base API and data used to render a Hitchhiker
 * page.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 */
abstract class HitchhikerController
{
    /**
     * Stores all information that is sent to Twig for rendering the page.
     * 
     * @var object $yt
     *   + useModularCore (bool, required) - Toggles base.js/core.js use by Hitchhiker.
     *   + modularCoreModules (string[]) - Defines base.js page modules.
     *   + spfEnabled (bool, required) - Enables YouTube SPF (soft loading).
     *   + spf (bool, required) - True if the page is navigated to via SPF.
     *   + title (string) - Page title name
     *   + appbar (object) - Available in NirvanaController; defines YouTube Appbar.
     *   + page (object) - Page metadata
     */
    protected $yt;

    /**
     * Defines the default page template.
     * 
     * This may be overridden for certain contexts in an onGet()
     * callback.
     * 
     * @var string
     */
    public $template = "";

    /**
     * Defines the default element IDs that are listened to by
     * YouTube's SPF library.
     * 
     * This defines what elements get changed with every soft navigation.
     * 
     * @var string[]
     */
    protected $spfIdListeners = [
        'player-unavailable<class>',
        'alerts',
        'content',
        '@page<class>',
        'player-playlist<class>',
        '@player<class>'
    ];

    /**
     * Implements the base functionality that is ran on every request.
     * 
     * This function should not be overridden for page-specific
     * functionality. Use the controller's API (onGet()) for that.
     * 
     * @param object $yt                 Template data.
     * 
     * @param string $template           Passes a template in and out of the function.
     *                                   For API usage, you can safely ignore this. It only
     *                                   matters on the technical end.
     * 
     * @param RequestMetadata $request   Reports request metadata.
     * 
     * @return void
     */
    public function get(&$yt, &$template, $request)
    {
        $this->yt = &$yt;
        $this->init($yt, $template);

        $this->onGet($yt, $request);

        $this->postInit($yt, $template);

        $this->doGeneralRender();
    }

    /**
     * Defines the API for handling GET requests. Pages should always use this;
     * only subcontrollers may override get() directly.
     * 
     * @param object $yt                Template data.
     * @param RequestMetadata $request  Reports request metadata.
     * 
     * @return void
     */
    abstract public function onGet(&$yt, $request);

    /**
     * Set initial variables for this controller type.
     * 
     * @param $yt        Template data.
     * @param $template  Backend template data.
     * 
     * @return void
     */
    protected function init(&$yt, &$template)
    {
        $yt->spfEnabled = false;
        $yt->useModularCore = false;
        $yt->page = (object)[];
    }

    /**
     * Defines the tasks performed after the page is done being built.
     * 
     * Mainly, this prepares data internally to prepare sending to Twig.
     * 
     * @param $yt        Template data.
     * @param $template  Backend template data.
     * 
     * @return void
     */
    public function postInit(&$yt, &$template)
    {
        $template = $this->template;
    }

    /**
     * Perform a Twig render, accounting for SPF status if it is enabled, and
     * reporting the debugger if it is enabled.
     * 
     * @return void
     */
    public function doGeneralRender()
    {
        /*
         * Expose the debugger if it is enabled. All necessary checks are performed
         * within this function, so all that needs to be done here is calling it.
        */
        \Rehike\Debugger\Debugger::expose();

        // Capture the render so that we may send it through SpfPhp.
        $capturedRender = TemplateManager::render();

        if (SpfPhp::isSpfRequested() && $this->yt->spfEnabled)
        {
            // Skip serialisation so that the output may be modified. (also 
            // suppress warnings; idk why (buggy library lol))
            $spf = @SpfPhp::parse($capturedRender, $this->spfIdListeners, [
                "skipSerialization" => true
            ]);

            // Post-data generation callback for custom handling
            $this->handleSpfData($spf);

            header("Content-Type: application/json");

            echo json_encode($spf);
        }
        else
        {
            // In the case this is not an SPF request, we don't have to do anything.
            echo $capturedRender;
        }
    }

    /**
     * Modify generated SPF data before it's sent to the client.
     * 
     * For example, adding custom metadata to the response.
     * 
     * @param object $data reference
     * @return void
     */
    public function handleSpfData(&$data) {}
}