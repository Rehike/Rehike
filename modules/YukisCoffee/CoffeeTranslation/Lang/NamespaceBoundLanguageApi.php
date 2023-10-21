<?php
namespace YukisCoffee\CoffeeTranslation\Lang;

use Rehike\Logging\DebugLogger;
use YukisCoffee\CoffeeTranslation\Lang\LanguageApi;

/**
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class NamespaceBoundLanguageApi
{
    /**
     * The current namespace to be used.
     */
    private string $uri;
    
    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function getAllTemplates(): object
    {
        return LanguageApi::getAllTemplates($this->uri);
    }

    public function get(string $name): string
    {
        return LanguageApi::getRawString($this->uri, $name);
    }

    public function format(string $name, mixed ...$args)
    {
        return LanguageApi::getFormattedString($this->uri, $name, ...$args);
    }

    public function formatDate(int $format, int $timestamp = 0): string
    {
        if (LanguageApi::tryFormatDateTime($format, $timestamp, $out))
        {
            return $out;
        }

        return "";
    }

    public function formatNumber(int $number, int $numberOfDecimalPoints = 0)
    {
        if (LanguageApi::tryFormatNumber($number, $result, $numberOfDecimalPoints))
        {
            return $result;
        }
        
        return "";
    }
}