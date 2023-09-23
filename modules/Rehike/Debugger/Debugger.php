<?php
namespace Rehike\Debugger;

use \Rehike\RehikeConfigManager;
use \Rehike\TemplateManager;
use \Rehike\i18n;
use \Rehike\YtApp;
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
     */
    protected static Context $context;

    /**
     * Reference to the global context.
     */
    protected static YtApp $yt;

    /**
     * Stores the result of getting the debugger's condensed status.
     * 
     * The debugger is condensed when it is disabled (it's not really ever
     * REALLY disabled because it's used for beautiful error message delivery
     * too).
     */
    public static bool $condensed = true;

    /**
     * Stores a log of all errors (not exceptions) that have occurred since
     * the debugger was registered.
     *  
     * @var ErrorWrapper[] 
     */
    protected static array $errors = [];

    /**
     * Get if the debugger is condensed.
     */
    public static function isCondensed(): bool
    {
        return self::$condensed;
    }

    /**
     * Initialise the debugger.
     */
    public static function init(YtApp $yt): void
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
     */
    public static function shutdown(): void
    {
        if (!in_array($_GET["rebug_get_info"] ?? "false", ["false", "0"]))
        {
            self::handleGetInfo();
        }
    }

    /**
     * Handle a page requested with ?rebug_get_info=1
     */
    public static function handleGetInfo(): void
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
     */
    public static function getInternalContext(): Context
    {
        return self::$context;
    }

    /**
     * Setup the tabs available to the debugger session.
     */
    public static function setupTabs(Dialog $context): void
    {
        $i18n = &i18n::getNamespace("rebug");

        /** @var ErrorTab */
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

            /** @var YtWalker */
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
     */
    public static function expose(): void
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
     */
    public static function exposeSpf(): object
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
     */
    public static function getErrorCount(): int
    {
        return count(self::$errors);
    }

    /**
     * Push an error to the debugger.
     */
    public static function pushError(ErrorWrapper $err): void
    {
        self::$errors[] = $err;
    }

    /**
     * Add data to the context.
     */
    public static function addContext(string $name, mixed $value): void
    {
        self::$context->{$name} = $value;
    }

    /**
     * Initialise i18n
     */
    protected static function setupI18n(): void
    {
        $i18n = &i18n::newNamespace("rebug");

        $i18n->registerFromFile("en", "i18n/rehike/debugger/en.json");
    }

    /**
     * Refresh the condensed status.
     */
    protected static function refreshInternalCondensedStatus(): void
    {
        self::$condensed = RehikeConfigManager::getConfigProp("advanced.enableDebugger")
            ? false
            : true;
    }
}

/**
 * Handles all errors and logs them in the debugger.
 */
function YcRehikeDebuggerErrorHandler(
        int $errno, 
        string $errstr, 
        string $errfile, 
        int $errline
): bool
{
    // Surpress @ operator
    $errorReporting = error_reporting();
    
    if (E_ALL != $errorReporting)
    {
        return false;
    }

    Debugger::pushError(
        new ErrorWrapper($errno, $errstr, $errfile, $errline)
    );

    return true;
}