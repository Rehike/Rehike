<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Loader;

use YukisCoffee\CoffeeTranslation\{
    Lang\Record\LanguageRecord,
    Lang\Culture\CultureInfo
};

/**
 * Declares information for a given language.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Language
{
    /**
     * Each of the language's namespace definitions.
     * 
     * @var LanguageRecord[]
     */
    public array $namespaces = [];

    /**
     * The language's culture information, if available.
     */
    public ?CultureInfo $cultureInfo = null;
}