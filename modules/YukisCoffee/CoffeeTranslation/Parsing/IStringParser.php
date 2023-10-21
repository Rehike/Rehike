<?php
namespace YukisCoffee\CoffeeTranslation\Parsing;

/**
 * A common interface for all string parser bases.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IStringParser
{
    /**
     * Get the current location of the cursor.
     */
    public function getCursor(): int;

    /**
     * Set the location of the cursor.
     */
    public function setCursor(int $value): void;

    /**
     * Get the size of the data.
     */
    public function getSize(): int;

    /**
     * Check if the cursor currently lies out of bounds.
     */
    public function isOutOfBounds(): bool;

    /**
     * Read the current character in the string.
     */
    public function read(
            int $offset = 0,
            int $amount = 1,
            bool $curse = false
    ): string;

    /**
     * Increment the cursor by one.
     */
    public function next(): self;

    /**
     * Decrement the cursor by one.
     */
    public function prev(): self;

    /**
     * Skip the cursor forward by a specified amount.
     */
    public function skip(int $amount): self;

    /**
     * Rewind the cursor backward by a specified amount.
     */
    public function rewind(int $amount): self;
}