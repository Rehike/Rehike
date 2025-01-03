<?php
namespace Rehike\ControllerV2;

/**
 * A store for custom handlers that the user registers.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class CallbackStore
{
    /**
     * The redirect handler callback.
     * 
     * Classes in PHP cannot actually have members of type callable, so this is
     * a hack:
     * 
     * @var callback
     */
    public static $handleRedirect;

    public static function setRedirectHandler(callable $cb): void
    {
        self::$handleRedirect = $cb;
    }
}