<?php

\Rehike\TemplateFunctions::register('contentType', function($type) {
   /*
    * PATCH (kirasicecreamm): Prevent "headers already sent" warning in the
    * case of multiple Twig calls, for example.
    */
   @header('Content-Type: ' . $type);
});