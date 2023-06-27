<?php

/**
 * Get the type of a variable.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @param string $data
 * @return string
 */
\Rehike\TemplateFunctions::register('getType', function($data) {
    return gettype($data);
});