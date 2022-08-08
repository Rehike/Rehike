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
    public static $handleRedirect;

    public static function setRedirectHandler($cb)
    {
        self::$handleRedirect = $cb;
    }
}