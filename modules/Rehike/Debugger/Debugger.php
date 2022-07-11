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
     * Stores the result of getting the debugger's enabled status.
     * 
     * @var bool
     */
    public static $enabled = false;

    /** @var ErrorWrapper[] */
    protected static $errors = [];

    /**
     * Get if the debugger is enabled.
     * 
     * @return bool
     */
    public static function isEnabled()
    {
        return self::$enabled;
    }

    /**
     * Initialise the debugger.
     * 
     * @param object $yt global state
     * @return void
     */
    public static function init(&$yt)
    {
        self::getEnabledStatus();

        if (self::$enabled)
        {
            self::$yt = &$yt;
            self::setupI18n();
            self::$context = (object)[];

            error_reporting(E_ALL);
            ini_set("display_errors", "off");

            TemplateManager::addGlobal("rehikeDebugger", self::$context);

            // Disable the CoffeeException custom error screen
            CoffeeException::disableBeautifulError();

            set_error_handler("\\Rehike\\Debugger\\YcRehikeDebuggerErrorHandler");
        }
    }

    /**
     * Expose the debugger to the templater.
     * 
     * @return void
     */
    public static function expose()
    {
        if (self::$enabled)
        {
            $i18n = &i18n::getNamespace("rebug");

            $context = &self::$context;

            $context->openButton = new OpenButton(self::getErrorCount());

            $context->dialog = new Dialog();

            $errorTab = &$context->dialog->addTab(
                ErrorTab::createTab(
                    $i18n->tabErrorTitle(number_format(self::getErrorCount())),
                    "error",
                    true
                )
            );
            $errorTab->pushErrors(self::$errors);

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
     * Refresh the enabled status.
     * 
     * @return void
     */
    protected static function getEnabledStatus()
    {
        self::$enabled = RehikeConfigManager::getConfigProp("enableRehikeDebugger")
            ?? false
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