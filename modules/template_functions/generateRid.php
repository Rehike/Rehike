<?php
/**
 * Generate a template level RID.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
\Rehike\TemplateFunctions::register('generateRid', function() {
    return rand(100000, 999999);
});