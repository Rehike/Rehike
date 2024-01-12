<?php
namespace Rehike\Ds;

use ArrayAccess, ArrayIterator, IteratorAggregate;

/**
 * Implements a general data array object that can be accessed both an array
 * and an object.
 * 
 * This is currently implemented as read-only.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class EasyAccessMap implements ArrayAccess, IteratorAggregate
{
    /**
     * Bound array that stores definitions.
     * 
     * @var array
     */
    private array $boundArray = [];

    public function __construct(array &$baseArray)
    {
        $this->boundArray = &$baseArray;
    }

    public function __get(mixed $var)
    {
        $lowercaseName = strtolower($var);

        // Converted from camelCase to hyphen-case
        $hyphenCaseName = strtolower(
            preg_replace("/(?<!^)[A-Z]/", "-$0", $var)
        );

        $snake_case_name = str_replace("_", "-", $var);

        // Check if the raw name is accessible in the object
        if (isset($this->boundArray[$lowercaseName]))
        {
            return $this->boundArray[$lowercaseName];
        }
        // Otherwise, check if the camelCase name is accessible
        // in the object.
        else if (isset($this->boundArray[$hyphenCaseName]))
        {
            return $this->boundArray[$hyphenCaseName];
        }
        // And if that's not the case, check if it's snake_case
        else if (isset($this->boundArray[$snake_case_name]))
        {
            return $this->boundArray[$snake_case_name];
        }
        // And finally, if none of those are set, return null
        else
        {
            return null;
        }
    }

    public function __isset(string $var): bool
    {
        return null != $this->__get($var);
    }

    public function __set(string $a, mixed $b): void
    {
        $this->offsetSet(null, null); // inherit warning
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->boundArray[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        trigger_error(
            "Rehike\Ds\EasyAccessMap is read only.", 
            E_USER_WARNING
        );
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->offsetSet(null, null); // inherit warning
    }

    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->boundArray[$offset])
            ? $this->boundArray[$offset]
            : null
        ;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->boundArray);
    }
}