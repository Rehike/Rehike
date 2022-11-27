<?php
namespace Rehike\Debugger;

use \Rehike\RehikeConfigManager;
use \Rehike\TemplateManager;
use \Rehike\i18n;
use \YukisCoffee\CoffeeException;

use \Rehike\Model\Rehike\Debugger\{
    MOpenButton as OpenButton,
    MDialog as Dialog,
    MErrorTab as ErrorTab,
    MYtWalker as YtWalker,
    MLoadingTab as LoadingTab,
    MNetworkTab as NetworkTab
};

/**
 * Implements the PHP end of Rehike Debugger/Rebug.
 * 
 * A comment on nomenclature: We tend to call it the debugger in PHP land, 
 * whereas it's called Rebug on the HTML and JS side of things.
 * 
 * HTML and JS lands are implemented in template/hitchhiker/rehike/debugger.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class Debugger
{
    /**
     * Stores the common context of this session's debugger.
     * 
     * @var Context
     */
    protected static $context;

    /**
     * Reference to the global context.
     * 
     * @var object
     */
    protected static $yt;

    /**
     * Stores the result of getting the debugger's condensed status.
     * 
     * The debugger is condensed when it is disabled (it's not really ever
     * REALLY disabled because it's used for beautiful error message delivery
     * too).
     * 
     * @var bool
     */
    public static $condensed = true;

    /**
     * Stores a log of all errors (not exceptions) that have occurred since
     * the debugger was registered.
     *  
     * @var ErrorWrapper[] 
     */
    protected static $errors = [];

    /**
     * Get if the debugger is condensed.
     * 
     * @return bool
     */
    public static function isCondensed()
    {
        return self::$condensed;
    }

    /**
     * Initialise the debugger.
     * 
     * @param object $yt global state
     * @return void
     */
    public static function init(&$yt)
    {
        self::refreshInternalCondensedStatus();

        // Variable walker data should only be
        // exposed if the debugger is enabled
        if (!self::$condensed) self::$yt = &$yt;

        self::setupI18n();
        self::$context = new Context();

        error_reporting(E_ALL);
        //ini_set("display_errors", "off");

        TemplateManager::addGlobal("rehikeDebugger", self::$context);

        // Disable the CoffeeException custom error screen
        CoffeeException::disableBeautifulError();

        set_error_handler("\\Rehike\\Debugger\\YcRehikeDebuggerErrorHandler");
    }

    /**
     * Runs right before a standard page shutdown.
     * 
     * @return void
     */
    public static function shutdown()
    {
        if (!in_array($_GET["rebug_get_info"] ?? "false", ["false", "0"]))
        {
            self::handleGetInfo();
        }
    }

    /**
     * Handle a page requested with ?rebug_get_info=1
     */
    public static function handleGetInfo()
    {
        $headers = headers_list();
        
        // normalise
        foreach ($headers as $index => $value)
        {
            list($key, $value) = explode(': ', $value);
        
            unset($headers[$index]);
        
            $headers[$key] = $value;
        }

        $originalContentType = $headers["Content-Type"] ?? "text/html";
        
        header("X-Rebug-Get-Info: true");
        header("Content-Type: application/json");

        $pageContent = ob_get_clean();
        
        $response = (object)[];
        $response->content_type = $originalContentType;
        $response->rebug_data = self::exposeSpf();
        $response->response = $pageContent;

        echo json_encode($response);
    }

    /**
     * Get the internal context used by the debugger.
     * 
     * @return object
     */
    public static function getInternalContext()
    {
        return self::$context;
    }

    /**
     * Setup the tabs available to the debugger session.
     * 
     * @param Dialog|FullPage $context
     * @return void
     */
    public static function setupTabs($context)
    {
        $i18n = &i18n::getNamespace("rebug");

        $errorTab = $context->addTab(
            ErrorTab::createTab(
                $i18n->tabErrorTitle(number_format(self::getErrorCount())),
                "error",
                true
            )
        );
        $errorTab->pushErrors(self::$errors);

        if (!self::$condensed)
        {
            /*
            $context->addTab(
                NetworkTab::createTab(
                    $i18n->tabNetworkTitle,
                    "network"
                )
            );
            */

            $ytWalker = $context->addTab(
                YtWalker::createTab(
                    $i18n->tabYtWalkerTitle, "global_walker"
                )
            );
            $ytWalker->addYt(self::$yt);
        }
    }

    /**
     * Expose the debugger to the templater.
     * 
     * @return void
     */
    public static function expose()
    {
        $i18n = &i18n::getNamespace("rebug");

        $context = &self::$context;

        $context->condensed = self::$condensed;

        $context->openButton = new OpenButton(self::getErrorCount(), self::$condensed);

        $context->dialog = new Dialog(self::$condensed);

        self::setupTabs($context->dialog);
    }

    /**
     * Expose the debugger to an SPF response.
     * 
     * @return object
     */
    public static function exposeSpf()
    {
        self::expose();

        $context = self::$context;

        $response = (object)[];

        $response->updatedTabs = [];

        foreach ($context->getTabs() as $tab) if ($tab->content->enableJsHistory)
        {
            $html = TemplateManager::render(
                ["tab" => $tab], "rehike/debugger/spf/tab_content"
            );

            $response->updatedTabs += [
                $tab->id => [
                    "title" => $tab->title,
                    "html" => $html
                ]
            ];
        }

        $response->openButton = TemplateManager::render(
            [], "rehike/debugger/open_button"
        );

        if (!self::$condensed)
        {
            $response->globalWalker = (object)[];
            $response->globalWalker->data = (object)[];
            $response->globalWalker->data->yt = self::$yt;
            $response->globalWalker->attr = $context->jsAttrs;
        }

        return $response;
    }

    /**
     * Get the current error count.
     * 
     * @return int
     */
    public static function getErrorCount()
    {
        return count(self::$errors);
    }

    /**
     * Push an error to the debugger.
     * 
     * @param ErrorWrapper $err
     * @return void
     */
    public static function pushError($err)
    {
        self::$errors[] = $err;
    }

    /**
     * Add data to the context.
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public static function addContext($name, $value)
    {
        self::$context->{$name} = $value;
    }

    /**
     * Initialise i18n
     * 
     * @return void
     */
    protected static function setupI18n()
    {
        $i18n = &i18n::newNamespace("rebug");

        $i18n->registerFromFile("en", "i18n/rehike/debugger/en.json");
    }

    /**
     * Refresh the condensed status.
     * 
     * @return void
     */
    protected static function refreshInternalCondensedStatus()
    {
        self::$condensed = RehikeConfigManager::getConfigProp("advanced.enableDebugger")
        ? false
        : true
        ;
    }
}

/**
 * Handles all errors and logs them in the debugger.
 * 
 * @return void
 */
function YcRehikeDebuggerErrorHandler($errno, $errstr, $errfile, $errline)
{
    // Surpress @ operator
    $errorReporting = error_reporting();
    if (
        E_ALL != $errorReporting
    )
    {
        return false;
    }

    switch ($errno)
    {
        case E_USER_ERROR:
        case E_ERROR:
            // Call the general fatal handler
            \fatalHandler();
            break;
        default:
            Debugger::pushError(
                new ErrorWrapper($errno, $errstr, $errfile, $errline)
            );
            break;
    }
}