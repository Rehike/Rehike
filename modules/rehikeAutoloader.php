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
    if (YcFileExists("modules/{$filename}.php"))
    {
        require "modules/{$filename}.php";
    }
    else if (YcFileExists("modules/generated/{$filename}.php"))
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

/**
 * Checks if a file exists, relative to the document root.
 * 
 * This is required because PHP's default behaviour may, but doesn't
 * always, resort to the document root. This is a safer function as a result.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
function YcFileExists(string $filename): bool
{
    return file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $filename);
}

spl_autoload_register('YcRehikeAutoloader');