<?php
namespace Rehike\i18n\Internal\Parsing;

use Rehike\Attributes\StaticClass;
use Rehike\i18n\i18n;
use Rehike\i18n\Internal\Lang\SourceInfo;
use Rehike\i18n\Internal\Exception\UnsupportedException;

use Exception;

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
        $mbstringSupported = i18n::getIsMbStringSupported();
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

        throw new Exception(sprintf(
            "Failed to get string parser for file \"%s\".",
            $file->getName()
        ));

        NO_MBSTRING_EXCEPTION:
            throw new Exception(sprintf(
                "The runtime environment does not support the " .
                "%s encoding type (mbstring extension required).",
                $encodingType
            ));
    }
}