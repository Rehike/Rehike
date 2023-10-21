<?php
namespace YukisCoffee\CoffeeTranslation\Lang\Record;

use stdClass;
use ArrayAccess;
use Iterator;

/**
 * Represents an entries list in an i18n record.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class RecordEntries extends stdClass implements ArrayAccess
{
    private int $iterationPosition = 0;

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): bool
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $this->value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }

    public function current(): mixed
    {
        return $this->{$this->iterationPosition};
    }

    public function key(): mixed
    {
        return $this->iterationPosition;
    }

    public function next(): void
    {
        ++$this->iterationPosition;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->{$this->iterationPosition});
    }
}