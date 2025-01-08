<?php
namespace Rehike\ControllerV2;

/**
 * Implements core behaviours of the Rehike controller loading
 * architecture.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 * @version 3.0
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
     * @deprecated
     * 
     * @var object|array
     */
    public static object|array $state;

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
     * @deprecated
     * 
     * @var string
     */
    public static string $template;

    /**
     * @see CallbackStore::setRedirectHandler
     */
    public static function setRedirectHandler(callable $cb): void
    {
        CallbackStore::setRedirectHandler($cb);
    }
}