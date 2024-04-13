<?php
namespace YukisCoffee\CoffeeTranslation\Lang;

use YukisCoffee\CoffeeTranslation\{
    Lang\Loader\Language,
    Lang\Record\LanguageRecord,
    Lang\Culture\CultureInfo,
    Lang\Culture\CultureDateTimeInfo,
    Lang\Culture\WritingDirection,
    Exception\FailureException,
    Router\IRouter,
    CoffeeTranslation
};

/**
 * Manages language definitions loading.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class Loader
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Contains a list of all loaded languages during the session.
     * 
     * This is an associative array where the key is the ID of the language
     * whose definitions are stored.
     * 
     * @var Language[]
     */
    private static array $languages = [];

    public static function tryOpen(
            string $langId,
            string $namespace,
            ?LanguageRecord &$out,
            ?Language $languageObj = null,
    ): bool
    {
        if (self::tryLoadFromCache($langId, $namespace, $result))
        {
            $out = $result;
            return true;
        }

        if (null == $languageObj)
            $language = self::openLanguage($langId);
        else
            $language = $languageObj;

        if (self::tryLoadNewLanguageRecord($langId, $namespace, $result))
        {
            $language->namespaces[$namespace] = $result;

            $out = $result;
            return true;
        }

        $out = null;
        return false;
    }

    public static function tryGetCulture(
            string $langId,
            ?CultureInfo &$out
    ): bool
    {
        $language = self::openLanguage($langId);

        $out = $language->cultureInfo;
        return true;
    }

    private static function tryLoadFromCache(
            string $langId, 
            string $namespace,
            ?LanguageRecord &$out
    ): bool
    {
        if (isset(self::$languages[$langId]))
        {
            /** @var Language */
            $lang = self::$languages[$langId];

            if (isset($lang->namespaces[$namespace]))
            {
                $out = $lang->namespaces[$namespace];
                return true;
            }
        }

        $out = null;
        return false;
    }

    /**
     * Determines if a language is open.
     */
    private static function isLanguageOpen(string $langId): bool
    {
        return isset(self::$languages[$langId]);
    }

    /**
     * Opens a language store.
     */
    private static function openLanguage(string $langId): Language
    {
        if (self::isLanguageOpen($langId))
        {
            return self::$languages[$langId];
        }

        $lang = new Language;
        $culture = CoffeeTranslation::getConfigApi()->getCultureFileName();

        if (self::tryOpen($langId, $culture, $result, languageObj: $lang))
        {
            $lang->cultureInfo = self::loadCulture($result);
        }
        else
        {
            throw new \Exception("fuck $culture");
        }

        self::$languages[$langId] = $lang;

        return $lang;
    }

    /**
     * Try to load a new language record into memory.
     */
    private static function tryLoadNewLanguageRecord(
            string $langId,
            string $uri,
            ?LanguageRecord &$out
    ): bool
    {
        $router = CoffeeTranslation::getRouter();

        if ($router->locationExists($langId, $uri))
        {
            $record = $router->resolveLocation($langId, $uri)->record;

            if ($record instanceof LanguageRecord)
            {
                $out = $record;
                return true;
            }
        }

        $out = null;
        return false;
    }

    /**
     * Builds culture information from the culture record.
     */
    private static function loadCulture(LanguageRecord $data): CultureInfo
    {
        $result = new CultureInfo;

        if ($data->tryGetStringProperty("languageName", $_))
            $result->languageName = $_;
        else
            throw new FailureException("Unspecified language name in culture");

        if ($data->tryGetStringProperty("expandedLanguageName", $_))
            $result->expandedLanguageName = $_;

        if ($data->tryGetStringProperty("writingDirection", $_))
        {
            switch (strtoupper($_))
            {
                case "LTR":
                    $result->writingDirection = WritingDirection::LTR;
                    break;

                case "RTL":
                    $result->writingDirection = WritingDirection::RTL;
                    break;
                
                default:
                    trigger_error(
                        sprintf(
                            "Invalid CultureInfo::writingDirection value " .
                            "\"%s\". Assuming LTR.",
                            $_
                        ),
                        E_USER_WARNING
                    );
                    break;
            }
        }

        if ($data->tryGetStringProperty("thousandsSeparator", $_))
            $result->thousandsSeparator = $_;

        if ($data->tryGetStringProperty("decimalSeparator", $_))
            $result->decimalSeparator = $_;

        if ($data->tryGetStringProperty("spaceOnLineBreak", $_))
            $result->spaceOnLineBreak = $_;

        $dt = new CultureDateTimeInfo;
        $result->dateTimeInfo = $dt;

        if ($data->tryGetStringProperty("am", $_))
            $dt->am = $_;

        if ($data->tryGetStringProperty("pm", $_))
            $dt->pm = $_;

        if ($data->tryGetStringProperty("standardDateOrder", $_))
            $dt->standardDateOrder = $_;

        if ($data->tryGetStringProperty("expandedDateWithTime", $_))
            $dt->expandedDateWithTime = $_;

        if ($data->tryGetStringProperty("expandedDate", $_))
            $dt->expandedDate = $_;

        if ($data->tryGetStringProperty("dateWithTime", $_))
            $dt->dateWithTime = $_;

        if ($data->tryGetStringProperty("date", $_))
            $dt->date = $_;

        if ($data->tryGetStringProperty("shortDateWithTime", $_))
            $dt->shortDateWithTime = $_;

        if ($data->tryGetStringProperty("shortDate", $_))
            $dt->shortDate = $_;

        if ($data->tryGetStringProperty("time", $_))
            $dt->time = $_;

        if ($data->tryGetProperty("daysOfWeek", $_))
            $dt->daysOfWeek = (array)$_;

        if ($data->tryGetProperty("shortDaysOfWeek", $_))
            $dt->shortDaysOfWeek = (array)$_;

        if ($data->tryGetProperty("monthNames", $_))
            $dt->monthNames = (array)$_;

        if ($data->tryGetProperty("shortMonthNames", $_))
            $dt->shortMonthNames = (array)$_;

        return $result;
    }
}