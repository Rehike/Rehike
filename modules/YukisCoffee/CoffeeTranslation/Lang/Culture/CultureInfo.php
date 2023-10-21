<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Culture;

/**
 * Describes cultural information about a given language.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class CultureInfo
{
    /**
     * The display name of the language.
     * 
     * For example, "English" or "日本語".
     */
    public string $languageName;

    /**
     * The expanded display name of the language, if applicable.
     * 
     * For example, "English (United States)".
     */
    public ?string $expandedLanguageName = null;

    /**
     * The base language ID, if applicable.
     * 
     * For example, en-GB may extend from en-US.
     */
    public ?string $baseLanguageId = null;

    /**
     * The writing direction of the language.
     * 
     * @var WritingDirection
     */
    public int $writingDirection = WritingDirection::LTR;

    /**
     * The language's standard thousands separator.
     * 
     * For example, English speakers will use "32,000" and German speakers will
     * use "32.000".
     */
    public string $thousandsSeparator = ",";

    /**
     * The language's standard decimal separator.
     * 
     * For example, English speakers will use "12.5" and German speakers will
     * use "12,5".
     */
    public string $decimalSeparator = ".";

    /**
     * Whether or not the language should place a line break when parsing
     * multiline strings.
     * 
     * For example, English and most other languages that use the Latin script
     * should do this, but languages like Chinese or Japanese should not.
     */
    public bool $spaceOnLineBreak = false;

    /**
     * The culture's date/time information.
     */
    public CultureDateTimeInfo $dateTimeInfo;
}