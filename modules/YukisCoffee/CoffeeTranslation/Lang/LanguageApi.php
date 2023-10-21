<?php
namespace YukisCoffee\CoffeeTranslation\Lang;

use YukisCoffee\CoffeeTranslation\Attributes\StaticClass;
use YukisCoffee\CoffeeTranslation\CoffeeTranslation;
use YukisCoffee\CoffeeTranslation\Exception\FailureException;
use YukisCoffee\CoffeeTranslation\Lang\Culture\DateTimeApi;
use YukisCoffee\CoffeeTranslation\Util\UriUtils;
use YukisCoffee\CoffeeTranslation\DateTimeFormats;

/**
 * Implements various language APIs.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
class LanguageApi
{
    /**
     * Determines if the given language ID is supported.
     */
    public static function isValidLanguageId(string $langId): bool
    {
        return CoffeeTranslation::getRouter()->languageExists($langId);
    }

    public static function tryFormatDateTimeForLanguage(
            string $langId,
            int $format,
            int $timestamp = 0,
            ?string &$out
    ): bool
    {
        if (Loader::tryGetCulture($langId, $culture))
        {
            $dateTime = new DateTimeApi($culture);

            $out = $dateTime->formatDateTime($format, $timestamp);
            return true;
        }

        $out = null;
        return false;
    }

    public static function tryFormatDateTime(
            int $format,
            int $timestamp = 0,
            ?string &$out
    )
    {
        $langs = UriUtils::getLanguageListForUri("");

        foreach ($langs as $langId)
        {
            if (self::tryFormatDateTimeForLanguage($langId, $format, $timestamp, $result))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function formatDateTimeForLanguage(
            string $langId,
            int $format,
            int $timestamp =0
    ): string
    {
        if (self::tryFormatDateTimeForLanguage($langId, $format, $timestamp, $result))
        {
            return $result;
        }

        throw new FailureException(sprintf(
            "Failed to format date time for language \"%s\"",
            $langId
        ));
    }

    public static function formatDateTime(int $format, int $timestamp =0): string
    {
        if (self::tryFormatDateTime($format, $timestamp, $result))
        {
            return $result;
        }

        throw new FailureException("Failed to format date time");
    }

    public static function tryFormatNumberForLanguage(
            string $langId,
            int $number,
            ?string &$out,
            int $numberOfDecimalPoints = 0
    ): bool
    {
        if (Loader::tryGetCulture($langId, $culture))
        {
            $out = number_format(
                $number,
                $numberOfDecimalPoints,
                $culture->decimalSeparator,
                $culture->thousandsSeparator
            );
            return true;
        }
        else
        {
            $out = number_format($number, $numberOfDecimalPoints);
            return true;
        }
    }

    public static function tryFormatNumber(
            int $number,
            ?string &$out,
            int $numberOfDecimalPoints = 0
    ): bool
    {
        $langs = UriUtils::getLanguageListForUri("");

        foreach ($langs as $langId)
        {
            if (self::tryFormatNumberForLanguage($langId, $number, $result, $numberOfDecimalPoints))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function formatNumberForLanguage(
            string $langId,
            int $number, 
            int $numDecPoints = 0
    ): string
    {
        if (self::tryFormatNumberForLanguage($langId, $number, $result, $numDecPoints))
        {
            return $result;
        }

        throw new FailureException("Failed to format number.");
    }

    public static function formatNumber(int $number, int $numDecPoints = 0): string
    {
        if (self::tryFormatNumber($number, $result, $numDecPoints))
        {
            return $result;
        }

        throw new FailureException("Failed to format number.");
    }

    public static function tryGetAllTemplatesForLanguage(
            string $langId,
            string $uri,
            ?object &$out
    ): bool
    {
        if (Loader::tryOpen($langId, $uri, /*out*/ $record))
        {
            $out = $record->toObject();
            return true;
        }

        $out = null;
        return false;
    }

    public static function getAllTemplatesForLanguage(string $langId, string $uri): object
    {
        if (self::tryGetAllTemplatesForLanguage($langId, $uri, $result))
        {
            return $result;
        }
        
        throw new FailureException(sprintf(
            "Failed to get all templates for language \"%s\" with URI \"%s\"",
            $langId,
            $uri
        ));
    }

    /**
     * Try to get all templates of a given language namespace.
     */
    public static function tryGetAllTemplates(string $uri, ?object &$out): bool
    {
        $langs = UriUtils::getLanguageListForUri($uri);

        foreach ($langs as $langId)
        {
            if (self::tryGetAllTemplatesForLanguage($langId, $uri, $result))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function getAllTemplates(string $uri): object
    {
        if (self::tryGetAllTemplates($uri, $result))
        {
            return $result;
        }

        throw new FailureException(sprintf(
            "Failed to get all templates for URI \"%s\"",
            $uri
        ));
    }

    public static function tryGetRawStringForLanguage(
            string $langId,
            string $uri,
            string $path,
            ?string &$out
    ): bool
    {
        if (Loader::tryOpen($langId, $uri, $record))
        {
            if ($record->tryGetStringProperty($path, $str))
            {
                $out = $str;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function getRawStringForLanguage(
            string $langId,
            string $uri,
            string $path
    ): string
    {
        if (self::tryGetRawStringForLanguage($langId, $uri, $path, $result))
        {
            return $result;
        }

        throw new FailureException(sprintf(
            "Failed to get raw string for language \"%s\" with URI \"%s\" and path \"%s\"",
            $langId,
            $uri,
            $path
        ));
    }

    /**
     * Try to get a raw string in the given language namespace.
     */
    public static function tryGetRawString(string $uri, string $path, ?string &$out): bool
    {
        $langs = UriUtils::getLanguageListForUri($uri);

        foreach ($langs as $langId)
        {
            if (self::tryGetRawStringForLanguage($langId, $uri, $path, $result))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function getRawString(string $uri, string $path): string
    {
        if (self::tryGetRawString($uri, $path, $result))
        {
            return $result;
        }
        
        throw new FailureException(sprintf(
            "Failed to get raw string for URI \"%s\" with path \"%s\"",
            $uri,
            $path
        ));
    }

    public static function tryGetFormattedStringForLanguage(
            string $langId,
            string $uri,
            string $path,
            ?string &$out,
            string ...$args
    ): bool
    {
        if (self::tryGetRawStringForLanguage($langId, $uri, $path, $str))
        {
            $out = sprintf($str, ...$args);
            return true;
        }

        $out = null;
        return false;
    }

    public static function getFormattedStringForLanguage(
            string $langId,
            string $uri,
            string $path,
            string ...$args
    ): string
    {
        if (self::tryGetFormattedStringForLanguage($langId, $uri, $path, $result, ...$args))
        {
            return $result;
        }

        throw new FailureException(sprintf(
            "Failed to get formatted string for language \"%s\" with URI \"%s\" and path \"%s\"",
            $langId,
            $uri,
            $path
        ));
    }

    /**
     * Try to get a formatted string in the given language namespace.
     */
    public static function tryGetFormattedString(
            string $uri,
            string $path,
            ?string &$out,
            string ...$args
    ): bool
    {
        $langs = UriUtils::getLanguageListForUri($uri);

        foreach ($langs as $langId)
        {
            if (self::tryGetFormattedStringForLanguage($langId, $uri, $path, $result, ...$args))
            {
                $out = $result;
                return true;
            }
        }

        $out = null;
        return false;
    }

    public static function getFormattedString(string $uri, string $path, string ...$args): string
    {
        if (self::tryGetFormattedString($uri, $path, $result, ...$args))
        {
            return $result;
        }

        throw new FailureException(sprintf(
            "Failed to get formatted string for URI \"%s\" and path \"%s\"",
            $uri,
            $path
        ));
    }
}