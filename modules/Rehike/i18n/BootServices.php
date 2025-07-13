<?php
namespace Rehike\i18n;

use Rehike\ConfigManager\Config;
use Rehike\i18n\Internal\Core as I18nCore;
use Rehike\i18n\Internal\RehikeTranslationRouter;
use Rehike\YtApp;
use Rehike\Validation\ValidHostLanguages;

/**
 * Code organization stub for i18n boot services.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class BootServices
{
    /**
     * Perform setup duties.
     */
    public static function boot(): void
    {
        \Rehike\Profiler::start("i18nboot");
        $langId = Config::getConfigProp("hidden.language");

        i18n::getConfigApi()
            ->setRootDirectory($_SERVER["DOCUMENT_ROOT"] . "/i18n");
            
        if (isset($_COOKIE["hl"]))
        {
            $validator = new ValidHostLanguages();
            $targetHl = I18nCore::validateHlGl($_COOKIE["hl"]);
            
            if ($validator->validateString($targetHl))
            {
                $langId = $targetHl;
            }
        }

        if ($langId == null || !i18n::isValidLanguageId($langId))
        {
            $langId = "en-US";
        }
        
        i18n::setRouter(new RehikeTranslationRouter());

        self::initializeCoffeeTranslation($langId);
        
        // Make sure that the requested language can actually be loaded. If it cannot be, then
        // the code here will internally report a warning, and we'll set the language over to
        // en-US. This prevents an invalid language from making Rehike completely unusable.
        if (!Internal\Lang\Loader::ensureInitializationAndValidity($langId))
        {
            $langId = "en-US";
            self::initializeCoffeeTranslation($langId);
        }

        YtApp::getInstance()->gl = I18nCore::getInnertubeGeolocation();
        YtApp::getInstance()->hl = I18nCore::getInnertubeLanguageId();
        \Rehike\Profiler::end("i18nboot");
    }
    
    private static function initializeCoffeeTranslation(string $langId)
    {
        i18n::getConfigApi()
            ->setCultureFileName("_culture.i18n")
            ->setDefaultFileExtension("i18n")
            ->setDefaultLanguageId("en-US")
            ->setCurrentLanguageId($langId)
            ->setExceptionOnFailure(true)
        ;
    }
}