<?php
namespace Rehike\i18n\Internal\Util;

use Rehike\Attributes\StaticClass;

use Rehike\i18n\i18n;

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
            i18n::getConfigApi()->getPreferredLanguageIds();
        $result[] =
            i18n::getConfigApi()->getDefaultLanguageId();

        return $result;
    }
}