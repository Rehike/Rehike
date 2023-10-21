<?php
namespace YukisCoffee\CoffeeTranslation\Parsing;

/**
 * Implements a UTF-16 string parser.
 * 
 * This supports both big-endian and little-endian UTF-16 encoding types.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class Utf16StringParser implements IStringParser
{
    /**
     * The contents of the file from which definitions are to be loaded.
     */
    private string $sourceStr;
    
    /**
     * The current working byte in the source string.
     */
    private int $cursor = 0;

    /**
     * The size of the source data stream in bytes.
     */
    private int $size = 0;

    /**
     * Denotes if the UTF-16 data stream begins with a byte-order mark (0xFFFE,
     * 0xFEFF).
     */
    protected bool $hasByteOrderMark = false;

    /**
     * Denotes the encoding type of the given UTF-16 string.
     */
    protected string $endianEncoding = "UTF-16BE";

    public function __construct(string $source)
    {
        $this->sourceStr = $source;
        $this->size = strlen($source) - 1;

        $this->determineEncoding();

        // Skip the byte order mark if we have it (it's only used for encoding
        // detection purposes).
        if ($this->hasByteOrderMark)
        {
            $this->cursor = 2;
        }
    }

    /**
     * Determine the endianness sub-encoding of the source string.
     */
    protected function determineEncoding(): void
    {
        // Probably not a valid UTF-16 string in the first place lol
        if ($this->size < 2)
            return;

        $first = ord($this->sourceStr[0]);
        $second = ord($this->sourceStr[1]);

        // 0xFEFF: Big-endian UTF-16 magic
        if ($first == 0xFE && $second == 0xFF)
        {
            $this->endianEncoding = "UTF-16BE";
            $this->hasByteOrderMark = true;
        }
        // 0xFFFE: Little-endian UTF-16 magic
        else if ($first == 0xFF && $second == 0xFE)
        {
            $this->endianEncoding = "UTF-16LE";
            $this->hasByteOrderMark = true;
        }
        // No magic, assume big-endian
        else
        {
            $this->endianEncoding = "UTF-16BE";
            $this->hasByteOrderMark = false;
        }
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function setCursor(int $value): void
    {
        $this->cursor = $value;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function isOutOfBounds(): bool
    {
        return $this->cursor > $this->size || $this->cursor < 0;
    }

    public function read(
            int $offset = 0, 
            int $amount = 1,
            bool $curse = false
    ): string
    {
        if ($amount == 1)
        {
            $result = $this->sourceStr[$this->cursor + ($offset * 2)];
        }
        else
        {
            $result = substr(
                $this->sourceStr,
                $this->cursor + ($offset * 2),
                $amount * 2
            );
        }

        if ($curse)
        {
            $this->cursor += 2;
        }

        // lazy
        return mb_convert_encoding($result, "UTF-8", "UTF-16");
    }

    public function next(): self
    {
        $this->cursor += 2;
        return $this;
    }

    public function prev(): self
    {
        $this->cursor -= 2;
        return $this;
    }

    public function skip(int $amount): self
    {
        $this->cursor += $amount * 2;
        return $this;
    }

    public function rewind(int $amount): self
    {
        $this->cursor -= $amount * 2;
        return $this;
    }
}