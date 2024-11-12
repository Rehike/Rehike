<?php
namespace Rehike\i18n;

use Rehike\i18n\Internal\Core;

/**
 * API for retrieving Rehike-specific locale information for the current
 * session.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeLocale
{
    /**
     * Get the current Rehike language ID from CoffeeTranslation.
     */
    public static function getLanguageId(): string
    {
        $preferredLanguageIds = i18n::getConfigApi()->getPreferredLanguageIds();
        
        if (isset($preferredLanguageIds[0]))
        {
            return $preferredLanguageIds[0];
        }
        
        return i18n::getConfigApi()->getDefaultLanguageId();
    }
    
    /**
     * Get the current country ID.
     */
    public static function getCountryId(): string
    {
        return Core::getInnertubeGeolocation();
    }
    
    /**
     * Get the language ID to use for InnerTube.
     */
    public static function getInnertubeLanguageId(): string
    {
        static $cachedResult = null;
        
        if ($cachedResult)
            return $cachedResult;
        
        $cachedResult = Core::getInnertubeLanguageId();
        return $cachedResult;
    }
}