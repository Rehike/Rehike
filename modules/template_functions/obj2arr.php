<?php

/**
 * Convert an object to an associative array.
 * 
 * This is needed in order to iterate the keys of an object
 * in Twig. Twig only supports iterating associative arrays, not
 * objects.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @param string $obj to cast
 * @return array of the casted object
 */
\Rehike\TemplateFunctions::register('obj2arr', function($obj) {
    return (array)$obj;
});