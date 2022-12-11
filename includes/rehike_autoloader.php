<?php
/**
 * Declare and install the Rehike autoloader.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
function YcRehikeAutoloader($class)
{
    // Replace "\" in the filename with "/" to prevent
    // crashes on non-Windows operating systems.
    $filename = str_replace("\\", "/", $class);

    // Scan the file system for the requested module.
    if (file_exists("modules/{$filename}.php"))
    {
        require "modules/{$filename}.php";
    }
    else if (file_exists("modules/generated/{$filename}.php"))
    {
        require "modules/generated/{$filename}.php";
    }
    else if ("Rehike/Model/" == substr($filename, 0, 13))
    {
        $file = substr($filename, 13, strlen($filename));

        require "models/${file}.php";
    }
    else if ("Rehike/Controller" == substr($filename, 0, 17))
    {
        $file = substr($filename, 17, strlen($filename));

        require "controllers/${file}.php";
    }

    // Implement the fake magic method __initStatic
    // for automatically initialising static classses.
    if (method_exists($class, "__initStatic"))
    {
        $class::__initStatic();
    }
}

spl_autoload_register('YcRehikeAutoloader');