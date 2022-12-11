<?php
namespace YukisCoffee\CoffeeRequest\Network;

use ArrayAccess;
use Iterator;
use ReturnTypeWillChange; // PHP 8.1+

use function reset;
use function current;
use function key;
use function next;

/**
 * Implements an array for accessing HTTP headers.
 * 
 * This is very similar to the Controller v2 RequestMetadata structure
 * used by the Rehike project, except this is also iterable using foreach
 * loops.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
class ResponseHeaders implements ArrayAccess, Iterator
{
    /**
     * Bound array that stores definitions.
     * 
     * @var mixed[]
     */
    private array $boundArray = [];

    public function __construct(array $baseArray)
    {
        $this->boundArray = $baseArray;
    }

    /**
     * Attempt to get a property on the class if it is readable.
     */
    public function __get($var)
    {
        // Headers are case-insensitive.
        $lowercaseName = strtolower($var);

        // Converted from camelCase to hyphen-case
        $hyphenCaseName = strtolower(
            preg_replace("/(?<!^)[A-Z]/", "-$0", $var)
        );

        // And to snake_case
        $snake_case_name = str_replace("_", "-", $var);

        // Then check if the raw name is accessible in the object
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
        // If that's not the case, check if it's snake_case
        else if (isset($this->boundArray[$snake_case_name]))
        {
            return $this->boundArray[$snake_case_name];
        }
        // Finally, if none of those are set, return an empty string
        else
        {
            return "";
        }
    }

    public function __isset($var)
    {
        return "" != $this->__get($var);
    }

    public function __set($a, $b)
    {
        $this->offsetSet(null, null); // inherit warning
    }

    /*
     * Array access functions
     * 
     * In order to maintain compatibility with both PHP 7.x and
     * PHP 8.1, the ReturnTypeWillChange attribute is required on all
     * methods.
     * 
     * This is to avoid a conflict where PHP 8.1 requires strict type
     * signatures on methods and PHP 7.x doesn't even support them at
     * all.
     */

    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->boundArray[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        trigger_error("RequestMetadata->headers is read only.", E_USER_WARNING);
    }

    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->offsetSet(null, null); // inherit warning
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->boundArray[$offset])
            ? $this->boundArray[$offset]
            : null
        ;
    }

    #[ReturnTypeWillChange]
    public function rewind()
    {
        return reset($this->boundArray);
    }

    #[ReturnTypeWillChange]
    public function current()
    {
        return current($this->boundArray);
    }

    #[ReturnTypeWillChange]
    public function key()
    {
        return key($this->boundArray);
    }

    #[ReturnTypeWillChange]
    public function next()
    {
        return next($this->boundArray);
    }

    #[ReturnTypeWillChange]
    public function valid()
    {
        return key($this->boundArray) !== null;
    }
}