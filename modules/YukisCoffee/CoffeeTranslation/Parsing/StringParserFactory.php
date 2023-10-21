<?php
namespace YukisCoffee\CoffeeTranslation\Parsing;

use YukisCoffee\CoffeeTranslation\Attributes\StaticClass;
use YukisCoffee\CoffeeRequest\Exception\GeneralException as ExceptionGeneralException;
use YukisCoffee\CoffeeTranslation\CoffeeTranslation;
use YukisCoffee\CoffeeTranslation\Lang\SourceInfo;
use YukisCoffee\CoffeeTranslation\Exception\GeneralException;
use YukisCoffee\CoffeeTranslation\Exception\UnsupportedException;

/**
 * Helper for getting the correct string parser for a given encoding type.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
#[StaticClass]
final class StringParserFactory
{
    // Disable instantiation
    private function __construct() {}

    /**
     * Gets the correct string parser type for a given file.
     */
    public static function getStringParserForFile(SourceInfo $file): IStringParser
    {
        $mbstringSupported = CoffeeTranslation::getIsMbStringSupported();
        $encodingType = strtoupper($file->getEncodingType());

        switch ($encodingType)
        {
            case "ASCII":
            case "UTF-8":
                return new SingleByteStringParser($file->getContents());
            
            case "UTF-16":
            case "UTF-16BE":
                if (!$mbstringSupported)
                    goto NO_MBSTRING_EXCEPTION;
                return new Utf16StringParser($file->getContents());

            case "UTF-16LE":
                if (!$mbstringSupported)
                    goto NO_MBSTRING_EXCEPTION;
                return new Utf16LeStringParser($file->getContents());
        }

        throw new ExceptionGeneralException(sprintf(
            "Failed to get string parser for file \"%s\".",
            $file->getName()
        ));

        NO_MBSTRING_EXCEPTION:
            throw new UnsupportedException(sprintf(
                "The runtime environment does not support the " .
                "%s encoding type (mbstring extension required).",
                $encodingType
            ));
    }
}