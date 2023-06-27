<?php

/**
 * Convert a CONSTANT_CASE string to a class-case string.
 * 
 * This may be useful in a number of areas, but it's particularly
 * useful for the guide.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @param string $const in CONSTANT_CASE
 * @return string in class-case
 */
\Rehike\TemplateFunctions::register('const2class', function($const) {
    return strtolower(str_replace("_", "-", $const));
});