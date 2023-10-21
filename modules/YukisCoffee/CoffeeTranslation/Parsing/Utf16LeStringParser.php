<?php
namespace YukisCoffee\CoffeeTranslation\Parsing;

use YukisCoffee\CoffeeTranslation\Attributes\Override;
use YukisCoffee\CoffeeTranslation\Exception\GeneralException;

/**
 * Implements a little-endian UTF-16 string parser.
 * 
 * This is specifically a hack in order to allow independent constuction of a
 * little-endian type without a byte-order mark at the start of the file, and
 * without introducing additional parameters to the base constructor.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class Utf16LeStringParser extends Utf16StringParser
{
    #[Override]
    public function __construct(string $source)
    {
        parent::__construct($source);

        $this->endianEncoding = "UTF-16LE";
    }

    #[Override]
    final protected function determineEncoding(): void
    {
        parent::determineEncoding();

        if ("UTF-16BE" == $this->endianEncoding)
        {
            throw new GeneralException(
                "Big-endian UTF-16 input used in constructing strictly " .
                "little-endian parser"
            );
        }
    }
}