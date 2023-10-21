<?php
namespace YukisCoffee\CoffeeTranslation\Util;

use YukisCoffee\CoffeeTranslation\CoffeeTranslation;
use YukisCoffee\CoffeeTranslation\Attributes\StaticClass;

/**
 * Provides useful utilities for parsing translation URIs.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
final class UriUtils
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Try to get the language list of a given URI.
     */
    public static function getLanguageListForUri(string $uri): array
    {
        $result = [];

        $result =
            CoffeeTranslation::getConfigApi()->getPreferredLanguageIds();
        $result[] =
            CoffeeTranslation::getConfigApi()->getDefaultLanguageId();

        return $result;
    }
}