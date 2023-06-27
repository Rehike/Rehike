<?php
namespace YukisCoffee\CoffeeRequest\Exception;

/**
 * Thrown to notify a developer that no supported network handler
 * is present with their current PHP setup.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class NoSupportedHandlerException extends BaseHardMessageException
{
    protected const MESSAGE = (
        "Could not proceed with request as there is no available " .
        "network handler installed. (Please recompile PHP with the cURL " .
        "extension and try again)"
    );
}