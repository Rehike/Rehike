<?php
namespace Rehike\Controller\core;

use Rehike\TemplateManager;
use Rehike\Request;
use Rehike\Player\PlayerCore;
use SpfPhp\SpfPhp;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\Model\Guide\MGuide as Guide;
use Rehike\Model\Footer\MFooter as Footer;
use Rehike\Model\Masthead\MMasthead as Masthead;

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
     * Stores information about the current page endpoint.
     * 
     * @var object
     */
    protected static $currentEndpoint;

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
     * Whether or not we should use a Twig template to render.
     * 
     * Some AJAX responses are so simple, that using a template
     * makes no sense.
     * 
     * @var boolean
     */
    public $useTemplate = true;

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
     * What the Content-Type header should be in the response
     * 
     * @var string
     */
    public $contentType = "text/html";

    /**
     * Implements the base functionality that is ran on every GET request.
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
        header("Content-Type: " .  $this -> contentType);
        $this->yt = &$yt;
        $this->init($yt, $template);
        $this->initPlayer($yt);

        $this->onGet($yt, $request);

        $this->postInit($yt, $template);

        if ($this->useTemplate) $this->doGeneralRender();
    }

    /**
     * Implements the base functionality that is ran on every POST request.
     * 
     * This function should not be overridden for page-specific
     * functionality. Use the controller's API (onPost()) for that.
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
    public function post(&$yt, &$template, $request)
    {
        header("Content-Type: " .  $this -> contentType);
        $this->yt = &$yt;
        $this->init($yt, $template);

        $this->onPost($yt, $request);

        $this->postInit($yt, $template);

        if ($this->useTemplate) $this->doGeneralRender();
    }

    /**
     * Initialise the player.
     * 
     * @param object $yt        Template data.
     * @return void
     */
    public function initPlayer(&$yt)
    {
        $playerConfig = PlayerCore::getInfo();

        $yt->playerConfig = $playerConfig;
    }

    /**
     * Request the guide and return the processed result.
     * 
     * As Rehike implements a Nirvana frontend primarily, this behaviour
     * is unused by the base Hitchhiker controller. This function
     * is used by NirvanaController.
     * 
     * @return object
     */
    public function getPageGuide()
    {
        $response = Request::innertubeRequest("guide", (object)[]);

        $guide = json_decode($response);

        return Guide::fromData($guide);
    }

    protected static $hasAsyncGuideRequest = false;

    /**
     * Asynchronously request the guide so that it can be worked
     * with later.
     * 
     * This provides a more optimal implementation of the above
     * function.
     * 
     * @return void
     */
    public function getGuideAsync()
    {
        self::$hasAsyncGuideRequest = true;

        Request::queueInnertubeRequest("_guide", "guide", (object)[]);
    }

    public function hasAsyncGuideRequest()
    {
        return self::$hasAsyncGuideRequest;
    }

    /**
     * Get the result of the asynchronous guide request.
     * 
     * @return object
     */
    public function getGuideAsyncResult()
    {
        $guide = Request::getResponses()["_guide"] ?? null;

        if (is_null($guide)) return null;

        return Guide::fromData(
            json_decode($guide)
        );
    }

    /**
     * Set the current page endpoint.
     * 
     * This is only used internally for coordinating the pages. More
     * specifically, it is used by the guide service to know which item
     * to select.
     * 
     * @param string $type of the endpoint
     * @param string $a (whatever the endpoint offers)
     */
    public function setEndpoint($type, $a)
    {
        $type = strtolower($type);

        // Will be casted to an object
        $data = [];

        switch ($type)
        {
            case "browse":
                $data["browseEndpoint"] = (object)[
                    "browseId" => $a
                ];
                break;
            case "url":
                $data["urlEndpoint"] = (object)[
                    "url" => $a
                ];
                break;
        }

        $data = (object)$data;

        self::$currentEndpoint = $data;
    }

    /**
     * Defines the API for handling GET requests. Pages should always use this;
     * only subcontrollers may override onGet() directly.
     * 
     * @param object $yt                Template data.
     * @param RequestMetadata $request  Reports request metadata.
     * 
     * @return void
     */
    public function onGet(&$yt, $request) {}

    /**
     * Defines the API for handling POST requests. Pages should always use this;
     * only subcontrollers may override onPost() directly.
     * 
     * @param object $yt                Template data.
     * @param RequestMetadata $request  Reports request metadata.
     * 
     * @return void
     */
    public function onPost(&$yt, $request) {}

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

        if ($this -> useTemplate) {
            $yt -> masthead = new Masthead(false);
            $yt -> footer = new Footer();
        }
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
        
        $yt->currentEndpoint = self::$currentEndpoint;
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

        if (SpfPhp::isSpfRequested() && $this->yt->spfEnabled)
        {
            // Report SPF status to the templater
            $this->yt->spf = true;

            // Capture the render so that we may send it through SpfPhp.
            $capturedRender = TemplateManager::render();

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
            $capturedRender = TemplateManager::render();

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
