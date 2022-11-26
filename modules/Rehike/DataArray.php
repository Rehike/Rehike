<?php
namespace Rehike;

use ArrayAccess, ArrayIterator, IteratorAggregate;
use ReturnTypeWillChange; // PHP 8.1+

/**
 * Implements a general data array object that can be
 * accessed both an array and an object.
 * 
 * This is currently implemented as read-only.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class DataArray implements ArrayAccess, IteratorAggregate
{
    /**
     * Bound array that stores definitions.
     * 
     * @var array
     */
    private $boundArray = [];

    public function __construct(&$baseArray)
    {
        $this->boundArray = &$baseArray;
    }

    public function __get($var)
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

    public function __isset($var)
    {
        return null != $this->__get($var);
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
    public function &getIterator()
    {
        return new ArrayIterator($this->boundArray);
    }
}