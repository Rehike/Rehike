<?php
/**
 * SPL autoloader for the Rehike project.
 * 
 * Carried over from Coffee's implementation from the old
 * codebase; maintaining credits...
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @license CC0
 */

function YcRehikeAutoloader($class)
{
    if (file_exists("{$class}.php")) {
        require "{$class}.php";
    } else if (file_exists("module/{$class}.php")) {
        require "module/{$class}.php";
    }
}

spl_autoload_register('YcRehikeAutoloader');