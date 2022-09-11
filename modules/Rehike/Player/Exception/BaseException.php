<?php
namespace Rehike\Player\Exception;

// Define separately based on the presence of CoffeeException.
if (class_exists("YukisCoffee\\CoffeeException", true))
{
    abstract class BaseException extends \YukisCoffee\CoffeeException {}
}
else
{
    abstract class BaseException extends \Exception {}
}