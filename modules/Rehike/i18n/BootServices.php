<?php
namespace Rehike\i18n;

use Rehike\Util\PrefUtils;
use Rehike\ConfigManager\Config;
use Rehike\i18n\Private\Core as I18nCore;
use Rehike\YtApp;

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
    public static function boot()
    {
        $langId = Config::getConfigProp("hidden.language");

        i18n::getConfigApi()
            ->setRootDirectory($_SERVER["DOCUMENT_ROOT"] . "/i18n");

        if ($langId == null || !i18n::isValidLanguageId($langId))
        {
            $langId = "en-US";
        }

        i18n::getConfigApi()
            ->setCultureFileName("_culture.i18n")
            ->setDefaultFileExtension("i18n")
            ->setDefaultLanguageId("en-US")
            ->setCurrentLanguageId($langId)
            ->setExceptionOnFailure(true)
        ;

        YtApp::getInstance()->gl = I18nCore::getInnertubeGeolocation();
        YtApp::getInstance()->hl = I18nCore::getInnertubeLanguageId();

        /* Set PREF values */
        $pref = isset($_COOKIE["PREF"])
        ? PrefUtils::decode($_COOKIE["PREF"])
        : (object)[];

        $pref->hl = I18nCore::getInnertubeLanguageId();
        $pref->gl = I18nCore::getInnertubeGeolocation();

        setcookie("PREF", PrefUtils::encode($pref));
    }
}