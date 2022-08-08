<?php
namespace Rehike\ControllerV2;

/**
 * Implements core behaviours of the Controller v2
 * architecture.
 * 
 * These behaviours include the storing the common state
 * and template variables. It also provides an API for
 * basic interaction with the system, i.e. importing.
 * 
 * The variables passed to all CV2 controllers are, in this
 * precise order:
 *    - &$state       Reference to the global state variable.
 *    - &$template    Reference to the global template variable.
 *    - $request      Contains information about the current
 *                    request.
 * ...and then any custom defined arguments after that.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * @version 2.0
 */
class Core
{
    /** 
     * A reference to global state variable.
     * 
     * This variable gets passed to each controller, but
     * modifications exceed it so that closing services
     * may access its contents.
     * 
     * @var object|array
     */
    public static $state;

    /** 
     * A reference to the global template file string.
     * 
     * This is mostly useless even with Rehike's own
     * implementation of Controller v2, but it was useful
     * to keep as a reference during the development of the
     * system.
     * 
     * Template rendering should ideally be done by the
     * controllers themselves.
     * 
     * @var string
     */
    public static $template;

    /** Register a state reference. @see $state */
    public static function registerStateVariable(&$state)
    {
        self::$state = &$state;
    }

    /** Register a template reference. @see $template */
    public static function registerTemplateVariable(&$template)
    {
        self::$template = &$template;
    }

    /**
     * Import a controller's file or pull it from the session
     * cache.
     * 
     * The contents are cached in order to allow reimports without
     * causing additional errors, such as in the event of a
     * function redeclaration.
     * 
     * @return GetControllerInstance
     */
    public static function import($controllerName, $appendPhp = true)
    {
        if (ControllerStore::hasController($controllerName))
        {
            // Import from cache
            $controller = ControllerStore::getController($controllerName);

            return new GetControllerInstance($controllerName, $controller);
        }
        else
        {
            // Import from file (or die)
            $imports = require $controllerName . ($appendPhp ? ".php" : "");

            ControllerStore::registerController($controllerName, $imports);

            return new GetControllerInstance($controllerName, $imports);
        }
    }

    /**
     * @see CallbackStore::setRedirectHandler
     */
    public static function setRedirectHandler($cb)
    {
        return CallbackStore::setRedirectHandler($cb);
    }
}