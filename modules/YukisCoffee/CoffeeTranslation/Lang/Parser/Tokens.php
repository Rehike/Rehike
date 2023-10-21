<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Parser;

/**
 * I18N language token definitions.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Tokens
{
    public const TOKEN_COMMENT = '#';
    public const TOKEN_COLON = ':';
    public const TOKEN_DOUBLE_QUOTES = '"';
    public const TOKEN_SINGLE_QUOTES = '\'';
    public const TOKEN_MULTILINE_STRING = '>';
    public const TOKEN_SPACE = ' ';
    public const TOKEN_TAB = "\t";
    public const TOKEN_CARRIAGE_RETURN = "\r";
    public const TOKEN_LINE_BREAK = "\n";
    public const TOKEN_ESCAPE = '\\';
    public const TOKEN_COMMA = ',';
    public const KEYWORD_TRUE = "true";
    public const KEYWORD_FALSE = "false";
}