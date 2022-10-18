<?php
namespace Rehike\Debugger;

use \Rehike\RehikeConfigManager;
use \Rehike\TemplateManager;
use \Rehike\i18n;
use \YukisCoffee\CoffeeException;

use \Rehike\Model\Rehike\Debugger\MOpenButton as OpenButton;
use \Rehike\Model\Rehike\Debugger\MDialog as Dialog;
use \Rehike\Model\Rehike\Debugger\MErrorTab as ErrorTab;
use \Rehike\Model\Rehike\Debugger\MYtWalker as YtWalker;

/**
 * Implements the Rehike Debugger.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class Debugger
{
    protected static $context;
    protected static $yt;

    /**
     * Stores the result of getting the debugger's condensed status.
     * 
     * @var bool
     */
    public static $condensed = true;

    /** @var ErrorWrapper[] */
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
        self::getCondensedStatus();

        // Variable walker data should only be
        // exposed if the debugger is enabled
        if (!self::$condensed) self::$yt = &$yt;

        self::setupI18n();
        self::$context = (object)[];

        error_reporting(E_ALL);
        ini_set("display_errors", "off");

        TemplateManager::addGlobal("rehikeDebugger", self::$context);

        // Disable the CoffeeException custom error screen
        CoffeeException::disableBeautifulError();

        set_error_handler("\\Rehike\\Debugger\\YcRehikeDebuggerErrorHandler");
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

        if (!self::$condensed || (self::$condensed && self::getErrorCount() > 0))
        {
            $context->openButton = new OpenButton(self::getErrorCount(), self::$condensed);
        }

        $context->dialog = new Dialog(self::$condensed);

        $context->condensed = self::$condensed;

        $errorTab = &$context->dialog->addTab(
            ErrorTab::createTab(
                $i18n->tabErrorTitle(number_format(self::getErrorCount())),
                "error",
                true
            )
        );
        $errorTab->pushErrors(self::$errors);

        if (!self::$condensed)
        {
            $ytWalker = &$context->dialog->addTab(YtWalker::createTab($i18n->tabYtWalkerTitle, "global_walker"));
            $ytWalker->addYt(self::$yt);
        }
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
    protected static function getCondensedStatus()
    {
        self::$condensed = RehikeConfigManager::getConfigProp("enableRehikeDebugger")
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