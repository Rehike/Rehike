<?php
namespace Rehike;

use stdClass;

use Rehike\Model\{
    Appbar\MAppbar,
    Masthead\MMasthead,
    Footer\MFooter
};

use Rehike\Player\PlayerInfo;

/**
 * Defines the global state for the Rehike application.
 * 
 * This is used as the primary information sent to the templater, and thus
 * contains all information needed to generate a valid YouTube page. It is 
 * also a huge mess.
 * 
 * A bit of history: this is an evolution of a project-long global object
 * named $yt. Before a codebase cleanup over a year after the project started,
 * this object was just an (object)[] (i.e. an stdClass).
 * 
 * As a result, this class is going to be a little messy. Moving to a proper 
 * class still allows us to benefit from type safety and IDE static analysis.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
class YtApp extends stdClass
{
    private static YtApp $instance;

    public function __construct()
    {
        self::$instance = $this;
        $this->spfConfig = (object)[];
    }

    public static function getInstance(): YtApp
    {
        return self::$instance;
    }

    /**
     * Stores the contents of the page.
     * 
     * This can be literally anything, so long as it is a valid object. Each
     * page has different content specifications, after all.
     * 
     * TODO (kirasicecreamm): Deprecate support for associative array page
     * types (i.e. comment service ajax).
     */
    public object|array $page;

    /**
     * The title of the page, as it appears in the browser.
     */
    public string $title = "YouTube";

    /**
     * Stores data for the masthead of the page.
     * 
     * This includes the entire top of the page with the search bar, guide
     * button, etc.
     */
    public MMasthead $masthead;

    /**
     * Stores data for the footer of the page.
     * 
     * The footer is displayed at the bottom of the page and contains links
     * to informational parts of YouTube (i.e. TOS), as well as contains user-
     * customisation buttons.
     */
    public MFooter $footer;

    /**
     * Data for the "appbar" navigation bar.
     * 
     * This is displayed under the masthead on the home page and channels,
     * and contains vertical tabs that follow the user.
     */
    public MAppbar $appbar;

    /**
     * Determines if the current session should allow further SPF navigations.
     * 
     * This is set by page-specific controllers.
     */
    public bool $spfEnabled = false;

    /**
     * Determines if the current session currently uses SPF navigation.
     * 
     * This is set to true before rendering by the SPF controller.
     */
    public bool $spf = false;

    /**
     * Stores SPF configuration properties.
     */
    public object $spfConfig;

    /**
     * Determines if the current page should use the revamped module system
     * used by YouTube since 2014.
     * 
     * This is set by page-specific controllers.
     */
    public bool $useModularCore = false;

    /**
     * Stores a set of modules to request the use of.
     * 
     * This is only applicable if $useModularCore is true, otherwise all
     * contents of this variable are ignored.
     */
    public array $modularCoreModules = [];

    /**
     * Stores information about the player configuration.
     * 
     * This is used for player bootstrapping on the client-side.
     */
    public PlayerInfo $playerConfig;

    /**
     * Client-side preferences of the user.
     */
    public object $PREF;

    /**
     * Stores common i18n message strings.
     */
    public array $msgs = [];

    /**
     * The Rehike host language.
     */
    public string $hl = "en-US";

    /**
     * The user's geolocation (as obtained from YouTube).
     */
    public string $gl = "US";
}