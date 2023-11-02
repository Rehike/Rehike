<?php
namespace Rehike\Controller\core;

use Rehike\{
    YtApp,
    TemplateManager,
    Network,
    i18n\i18n,
    ConfigManager\Config,
    SecurityChecker,
    ViewProperties,
    Spf\Spf
};

use Rehike\Model\{
    Guide\MGuide as Guide,
    Footer\MFooter as Footer,
    Masthead\MMasthead as Masthead,
    Common\MAlert,
    Rehike\Security\SecurityLightbox
};

use Rehike\Async\Promise;

use Rehike\Player\PlayerCore;
use SpfPhp\SpfPhp;
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
 * @author Daylin Cooper <dcoop2004@gmail.com>
 */
abstract class HitchhikerController
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
     *   + viewProps (object, required) - HTML/SPF shared view properties.
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
     * Defines the default element IDs that are listened to by
     * YouTube's SPF library.
     * 
     * This defines what elements get changed with every soft navigation.
     * 
     * @var string[]
     */
    protected array $spfIdListeners = [
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
    public function get(YtApp $yt, string &$template, RequestMetadata $request): void
    {
        header("Content-Type: " .  $this->contentType);
        $this->yt = $yt;
        $this->init($yt, $template);
        $this->initPlayer($yt);

        $this->onGet($yt, $request);

        Network::run();

        $this->postInit($yt, $template);

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
    public function post(YtApp $yt, string &$template, RequestMetadata $request): void
    {
        header("Content-Type: " .  $this->contentType);
        $this->yt = $yt;
        $this->init($yt, $template);

        $this->onPost($yt, $request);

        Network::run();

        $this->postInit($yt, $template);

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
     * Configures view properties for the page.
     */
    public function setupViewProps(ViewProperties $vp): void {}

    /**
     * Initialise the player.
     */
    public function initPlayer(YtApp $yt): void
    {
        $playerConfig = PlayerCore::getInfo();

        $yt->playerConfig = $playerConfig;
    }

    /**
     * Responsible for early initialization of SPF elements (page-inspecific).
     */
    public function internalInitSpfElements_(): void
    {
        $playerUnavailable = Spf::createElement(
            id: "player-unavailable",
            templateName: "page_fragments/player_unavailable"
        );
        $playerUnavailable->setAttribute(
            "class",
            "hid"
        );

        $alerts = Spf::createElement(
            id: "alerts",
            templateName: "alerts",
            blockBound: true
        );
        $alerts->setAttribute(
            "class",
            $this->yt->viewProps->leftAlignPage
                ? ""
                : "content-alignment"
        );

        $content = Spf::createElement(
            id: "content",
            templateName: "content",
            blockBound: true
        );
        $content->setAttribute(
            "class",
            $this->yt->viewProps->leftAlignPage
                ? ""
                : "content-alignment"
        );

        $page = Spf::createElement(id: "page");
        $page->setAttribute(
            "class",
            $this->yt->viewProps->pageClassName . " "
                . $this->yt->viewProps->pageClasses
        );

        $playerPlaylist = Spf::createElement(
            id: "player-playlist",
            templateName: "page_fragments/player_playlist"
        );
        $playerPlaylist->setAttribute(
            "class",
            isset($this->yt->page->playlist)
                ? "content-alignment  watch-player-playlist"
                : "hid"
        );

        $player = Spf::createElement(id: "player");
        $player->setAttribute("class", "off-screen");
    }

    /**
     * Page-specific SPF element changes.
     */
    public function initSpfElements(): void {}

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
                $data = $response->getJson();
                $guide = Guide::fromData($data);
                
                $resolve($guide);
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
    protected function init(YtApp $yt, string &$template): void
    {
        $yt->spfEnabled = false;
        $yt->useModularCore = false;
        $yt->page = (object)[];
        $yt->viewProps = new ViewProperties;

        $yt->viewProps->appbarEnabled = false;

        if ($this->useTemplate)
        {
            $yt->masthead = new Masthead(false);
            $yt->footer = new Footer();
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
    public function postInit(YtApp $yt, string &$template): void
    {
        $template = $this->template;
        $this->setupViewProps($yt->viewProps);
        $this->internalInitSpfElements_();
        $this->initSpfElements();
        
        if (isset(self::$currentEndpoint))
        {
            $yt->currentEndpoint = self::$currentEndpoint;
        }
        else
        {
            $yt->currentEndpoint = null;
        }

        if (!SecurityChecker::isSecure() && !SpfPhp::isSpfRequested())
        {
            $yt->rehikeSecurityNotice = new SecurityLightbox();
        }

        if (Config::getConfigProp("hidden.disableRehike") == true)
        {
            if (!isset($yt->page->alerts))
                $yt->page->alerts = [];

            $i18n = i18n::getNamespace("rehike/disable_rehike");
            
            $yt->page->alerts[] = new MAlert([
                "type" => MAlert::TypeWarning,
                "text" => $i18n->get("currentlyDisabledMessage"),
                "hasCloseButton" => false
            ]);
        }
    }

    /**
     * Perform a Twig render, accounting for SPF status if it is enabled, and
     * reporting the debugger if it is enabled.
     */
    public function doGeneralRender(): void
    {
        if (Spf::isSpfRequested() && $this->yt->spfEnabled)
        {
            // Report SPF status to the templater
            $this->yt->spf = true;

            // Capture the render so that we may send it through SpfPhp.
            //$capturedRender = TemplateManager::render();

            // Skip serialisation so that the output may be modified. (also 
            // suppress warnings; idk why (buggy library lol))
            // $spf = @SpfPhp::parse($capturedRender, $this->spfIdListeners, [
            //     "skipSerialization" => true
            // ]);
            $spf = $this->bakeNewSpfResponse();

            // Post-data generation callback for custom handling
            $this->handleSpfData($spf);

            if (is_object($spf))
                $spf->rebug_data = Debugger::exposeSpf();

            header("Content-Type: application/json");

            echo json_encode($spf);
        }
        else
        {
            /*
             * Expose the debugger if it is enabled. All necessary checks are performed
             * within this function, so all that needs to be done here is calling it.
             */
            Debugger::expose();

            $capturedRender = TemplateManager::render();

            // In the case this is not an SPF request, we don't have to do anything.
            echo $capturedRender;
        }
    }

    protected function bakeNewSpfResponse(): object
    {
        $result = (object)[];

        $result->title = $this->yt->title;
        //$result->head = "";
        $result->body = (object)[];
        $result->attr = (object)[];

        foreach (Spf::getAllElements() as $id => $element)
        {
            $body = "";

            if ($element->getTemplateName() != null)
            $body = $element->isBlockBound()
                ? TemplateManager::renderBlock($element->getTemplateName())
                : TemplateManager::render([], $element->getTemplateName());

            if (!empty($body))
                $result->body->{$id} = $body;
            $result->attr->{$id} = (object)[];

            foreach ($element->getAllAttributes() as $name => $value)
            {
                $result->attr->{$id}->{$name} = $value;
            }
        }

        //$result->foot = "";

        return $result;
    }

    /**
     * Modify generated SPF data before it's sent to the client.
     * 
     * For example, adding custom metadata to the response.
     * 
     * @param object $data reference
     * @return void
     */
    public function handleSpfData(object $data): void {}
}
