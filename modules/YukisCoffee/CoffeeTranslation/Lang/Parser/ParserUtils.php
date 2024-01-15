<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Parser;

use YukisCoffee\CoffeeTranslation\Attributes\StaticClass;

/**
 * Utilities for parsing i18n files.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
final class ParserUtils
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Determines if the input character is a numeric character.
     */
    public static function isNumberChar(string $char): bool
    {
        $i = ord($char);
        
        // ASCII range 48-57 are numbers 0-9
        return $i >= 48 && $i <= 57;
    }

    /**
     * Determines if the input character is an uppercase alphabetical character.
     */
    public static function isUpperAlphaChar(string $char): bool
    {
        $i = ord($char);

        // ASCII range 65-90 are letters A-Z
        return $i >= 65 && $i <= 90;
    }

    /**
     * Determines if the input character is a lowercase alphabetical character.
     */
    public static function isLowerAlphaChar(string $char): bool
    {
        $i = ord($char);

        // ASCII range 97-122 are letters a-z
        return $i >= 97 && $i <= 122;
    }

    /**
     * Determines if the input character is an alphabetical character.
     */
    public static function isAlphaChar(string $char): bool
    {
        return self::isLowerAlphaChar($char) || self::isUpperAlphaChar($char);
    }

    /**
     * Determines if the input character is an alphanumeric character.
     */
    public static function isAlphanumChar(string $char): bool
    {
        return (
            self::isNumberChar($char) || 
            self::isUpperAlphaChar($char) ||
            self::isLowerAlphaChar($char)
        );
    }

    /**
     * Determines if the input character may be used in a symbol name.
     */
    public static function isValidSymbolChar(string $char): bool
    {
        return (
            self::isAlphanumChar($char) ||
            '_' == $char ||
            '@' == $char
        );
    }

    /**
     * Determines if a given character is a non-0x20 space.
     */
    public static function isFakeSpaceChar(string $char): int
    {
        $code = ord($char);
        $fakeSpaces = [
            // NO-BREAK SPACE
            0xA0,
            // ORGHAM SPACE MARK
            0x1680,
            // EN QUAD
            0x2000,
            // EM QUAD
            0x2001,
            // EN SPACE
            0x2002,
            // EM SPACE
            0x2003,
            // THREE-PER-EM SPACE
            0x2004,
            // FOUR-PER-EM SPACE
            0x2005,
            // SIX-PER-EM SPACE
            0x2006,
            // FIGURE SPACE
            0x2007,
            // PUNCTUATION SPACE
            0x2008,
            // THIN SPACE
            0x2009,
            // HAIR SPACE
            0x200A,
            // NARROW NO-BREAK SPACE
            0x202F,
            // MEDIUM MATHEMATICAL SPACE
            0x205F,
            // IDEOGRAPHIC SPACE (CJK)
            0x3000
        ];

        return in_array($code, $fakeSpaces) ? $code : 0;
    }
}