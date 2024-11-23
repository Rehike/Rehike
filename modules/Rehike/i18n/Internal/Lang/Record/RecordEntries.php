<?php
namespace Rehike\i18n\Internal\Lang\Record;

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

    public function offsetGet(mixed $offset): mixed
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
    
    // Rehike-specific change for now:
    public static function __set_state(array $state): object
    {
        $pThis = new self();
        
        foreach ($state as $key => $value)
        {
            $pThis->{$key} = $value;
        }
        
        return $pThis;
    }
}