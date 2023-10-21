<?php
namespace YukisCoffee\CoffeeTranslation\Parsing;

/**
 * Implements a simple single-byte string parser.
 * 
 * This should be generally compatible with character sets that derive from
 * ASCII, including UTF-8. However, the resulting data will simply be parsed as
 * strictly single-byte, like ASCII.
 * 
 * Effectively, this limitation simply means that the parser can only work with
 * ASCII characters during the parsing process, but the input and output may
 * contain characters of other character sets (again, I have UTF-8 in mind).
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class SingleByteStringParser implements IStringParser
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

    public function __construct(string $source)
    {
        $this->sourceStr = $source;
        $this->size = strlen($source) - 1;
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
            $result = $this->sourceStr[$this->cursor + $offset];
        }
        else
        {
            $result = substr(
                $this->sourceStr,
                $this->cursor + $offset,
                $amount
            );
        }

        if ($curse)
        {
            $this->cursor++;
        }

        // echo "\n" . $this->cursor . ", " . $this->sourceStr[$this->cursor];

        return $result;
    }

    public function next(): self
    {
        $this->cursor++;
        return $this;
    }

    public function prev(): self
    {
        $this->cursor--;
        return $this;
    }

    public function skip(int $amount): self
    {
        $this->cursor += $amount;
        return $this;
    }

    public function rewind(int $amount): self
    {
        $this->cursor -= $amount;
        return $this;
    }
}