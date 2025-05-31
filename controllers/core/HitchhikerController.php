<?php
namespace Rehike\Controller\core;

use Rehike\{
    YtApp,
    TemplateManager,
    Network,
    i18n\i18n,
    ConfigManager\Config,
    SecurityChecker,
    Spf\Spf
};
use Rehike\Async\Concurrency;
use Rehike\Model\{
    Guide\MGuide,
    Footer\MFooter,
    Masthead\MMasthead,
    Common\MAlert,
    Rehike\Security\SecurityLightbox
};

use Rehike\Async\Promise;

use Rehike\Player\PlayerCore;
use Rehike\ControllerV2\RequestMetadata;
use Rehike\Debugger\Debugger;
use Rehike\DisableRehike\DisableRehike;

/**
 * Defines a general YouTube Hitchhiker controller.
 * 
 * This implements the base API and data used to render a Hitchhiker
 * page.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Isabella Lulamoon <kawapure@gmail.com>
 */
abstract class HitchhikerController extends PageController
{
    /**
     * Stores information about the current page endpoint.
     * 
     * @var object
     */
    protected static object $currentEndpoint;

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

    /**
     * Whether or not we should use a Twig template to render.
     * 
     * Some AJAX responses are so simple, that using a template
     * makes no sense.
     */
    public bool $useTemplate = true;

    /**
     * What the Content-Type header should be in the response
     * 
     * @var string
     */
    public string $contentType = "text/html";

    /**
     * Implements the base functionality that is ran on every GET request.
     * 
     * This function should not be overridden for page-specific
     * functionality. Use the controller's API (onGet()) for that.
     * 
     * @param YtApp $yt                  Template data.
     * 
     * @param string $template           Passes a template in and out of the function.
     *                                   For API usage, you can safely ignore this. It only
     *                                   matters on the technical end.
     * 
     * @param RequestMetadata $request   Reports request metadata.
     */
    public function get(): void
    {
        header("Content-Type: " .  $this->contentType);
        $this->yt = \Rehike\YtApp::getInstance();
        $this->init();

        $this->onGet($this->yt, $this->getRequest());

        Network::run();

        $this->postInit();

        if ($this->useTemplate) $this->doGeneralRender();
    }

    /**
     * Implements the base functionality that is ran on every POST request.
     * 
     * This function should not be overridden for page-specific
     * functionality. Use the controller's API (onPost()) for that.
     * 
     * @param YtApp $yt                  Template data.
     *
     * @param string $template           Passes a template in and out of the function.
     *                                   For API usage, you can safely ignore this. It only
     *                                   matters on the technical end.
     * 
     * @param RequestMetadata $request   Reports request metadata.
     */
    public function post(): void
    {
        header("Content-Type: " .  $this->contentType);
        $this->yt = \Rehike\YtApp::getInstance();
        $this->init();

        $this->onPost($this->yt, $this->getRequest());

        Network::run();

        $this->postInit();

        if ($this->useTemplate) $this->doGeneralRender();
    }

    public function setTitle(string $title): void
    {
        $yt = $this->yt;

        if (empty($title))
        {
            $yt->title = "YouTube";
        }
        else
        {
            $yt->title = $title . " - YouTube";
        }
    }

    /**
     * Initialise the player.
     *
     * @deprecated Moved to YtStateManager. This function is a no-op now.
     */
    public function initPlayer(YtApp $yt): void
    {
    }

    /**
     * Request the guide and return the processed result.
     *
     * As Rehike implements a Nirvana frontend primarily, this behaviour
     * is unused by the base Hitchhiker controller. This function
     * is used by NirvanaController.
     */
    public function getPageGuide(): Promise
    {
        return new Promise(function ($resolve) {
            Network::innertubeRequest("guide")->then(function ($response) 
                    use ($resolve)
            {
                // Need Concurrency::async to use yield on Guide::fromData()
                return Concurrency::async(function() use ($response, $resolve) {
                    $data = $response->getJson();
                    $guide = yield MGuide::fromData($data);

                    $resolve($guide);
                });
            });
        });
    }

    /**
     * Set the current page endpoint.
     * 
     * This is only used internally for coordinating the pages. More
     * specifically, it is used by the guide service to know which item
     * to select.
     * 
     * @param string $type of the endpoint
     * @param string $endpoint (whatever the endpoint offers)
     */
    public function setEndpoint(string $type, string $endpoint): void
    {
        $type = strtolower($type);

        // Will be casted to an object
        $data = [];

        switch ($type)
        {
            case "browse":
                $data["browseEndpoint"] = (object)[
                    "browseId" => $endpoint
                ];
                break;
            case "url":
                $data["urlEndpoint"] = (object)[
                    "url" => $endpoint
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
     */
    public function onGet(YtApp $yt, RequestMetadata $request): void {}

    /**
     * Defines the API for handling POST requests. Pages should always use this;
     * only subcontrollers may override onPost() directly.
     * 
     * @param object $yt                Template data.
     * @param RequestMetadata $request  Reports request metadata.
     */
    public function onPost(YtApp $yt, RequestMetadata $request): void {}

    /**
     * Set initial variables for this controller type.
     * 
     * @param $yt        Template data.
     * @param $template  Backend template data.
     * 
     * @return void
     */
    protected function init(): void
    {
        $this->yt->spfEnabled = false;
        $this->yt->useModularCore = false;
        $this->yt->page = (object)[];

        if ($this->useTemplate)
        {
            $this->yt->masthead = new MMasthead(false);
            $this->yt->footer = new MFooter();
        }
    }

    /**
     * Defines the tasks performed after the page is done being built.
     * 
     * Mainly, this prepares data internally to prepare sending to Twig.
     * 
     * @param $yt        Template data.
     * @param $template  Backend template data.
     */
    public function postInit(): void
    {
        $template = $this->template;
        
        if (isset(self::$currentEndpoint))
        {
            $this->yt->currentEndpoint = self::$currentEndpoint;
        }
        else
        {
            $this->yt->currentEndpoint = null;
        }

        if (!SecurityChecker::isSecure() && !Spf::isSpfRequested())
        {
            $this->yt->rehikeSecurityNotice = new SecurityLightbox();
        }

        if (Config::getConfigProp("hidden.disableRehike") == true)
        {
            if (!isset($this->yt->page->alerts))
                $this->yt->page->alerts = [];

            $i18n = i18n::getNamespace("rehike/disable_rehike");
            
            $this->yt->page->alerts[] = new MAlert([
                "type" => MAlert::TypeWarning,
                "text" => $i18n->get("currentlyDisabledMessage"),
                "hasCloseButton" => false
            ]);
        }
		
        if (isset($this->yt->masthead) && $this->yt->masthead instanceof MMasthead)
        {
            // Since we have a template, we should have a masthead, so we'll try to apply
            // the yoodle.
            $this->checkAndApplyYoodles($this->yt);
        }
    }
    
    /**
     * Manages the use of YouTube doodle logos for special events.
     */
    public function checkAndApplyYoodles(YtApp $yt): void
    {
        $curMonth = idate("m");
    
        if ($curMonth == 6)
        {
            $prideYoodleUrl = Config::getConfigProp("appearance.branding") != "BRANDING_2015"
                ? "/rehike/static/logo/pride_2017_custom.png"
                : "//s.ytimg.com/yts/img/doodles/yt_doodle_pride_2013-vflG2_e_y.png";
            
            $yt->masthead->applyYoodleLogo($prideYoodleUrl);
        }
    }

    /**
     * Perform a Twig render, accounting for SPF status if it is enabled, and
     * reporting the debugger if it is enabled.
     */
    public function doGeneralRender(): void
    {
        \Rehike\Profiler::start("template");

        if (Spf::isSpfRequested() && $this->yt->spfEnabled)
        {
            // Report SPF status to the templater
            $this->yt->spf = true;

            if ($this->tryGetSpfData($spfData))
            {
                $this->yt->spfConfig->data = $spfData;
            }

            // We pre-serialize Rebug data via SPF because it doesn't encode
            // anything in the Twig context itself. This is probably due to
            // recursion, but I don't care to look into it.
            $this->yt->spfConfig->rebugData = json_encode(Debugger::exposeSpf());

            $capturedRender = TemplateManager::render([], $this->template);

            header("Content-Type: application/json");
            echo $capturedRender;
        }
        else
        {
            /*
             * Expose the debugger if it is enabled. All necessary checks are performed
             * within this function, so all that needs to be done here is calling it.
             */
            Debugger::expose();

            $capturedRender = TemplateManager::render([], $this->template);

            // In the case this is not an SPF request, we don't have to do anything.
            echo $capturedRender;
        }
        
        \Rehike\Profiler::end("template");
    }

    /**
     * Used by certain controllers in order to supply custom data to be included
     * in an SPF response.
     * 
     * This is a try-get pattern, so the argument is the output, and the return
     * type is the status.
     */
    public function tryGetSpfData(?object &$data): bool
    {
        $data = null;
        return false;
    }
}
