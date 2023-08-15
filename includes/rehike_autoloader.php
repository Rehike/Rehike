<?php
namespace RehikeBase;

use Exception;

// Functions cannot be autoloaded, so load them manually.
require "includes/functions/async.php";
require "includes/functions/safe_class_alias.php";

/**
 * Implements the Rehike autoloader.
 * 
 * @version 2.0
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Autoloader
{
    // shhhh :3
    public const autoload = self::class . "::autoload";

    protected static array $classAliases = [];

    /**
     * The main autoloader function.
     */
    public static function autoload(string $class): void
    {
        // PHP classes are separated by "\", which works just fine on Windows,
        // but will fail on other operating systems. This is required in order
        // to load on *nix.
        $PATH = str_replace("\\", "/", $class);

        if (
            self::fileExists($f = "modules/$PATH.php") ||
            self::fileExists($f = "modules/generated/$PATH.php") ||
            self::mapPrefix($PATH, "Rehike/Model/", "models/", $f) ||
            self::mapPrefix($PATH, "Rehike/Controller/", "controllers/", $f)
        )
        {
            self::tryImportClass($f, $class);
        }

        // Implements the fake magic method __initStatic for automatically
        // initialising static classes.
        if (\method_exists($class, "__initStatic"))
        {
            $class::__initStatic();
        }
    }

    static function tryImportClass(string $path, string $class): void
    {
        @$status = include $path;

        if (true == $status)
        {
            if (
                \class_exists($class, false) ||
                \interface_exists($class, false) ||
                \trait_exists($class, false) ||
                (PHP_VERSION_ID > 81000 && \enum_exists($class, false))
            )
            {
                /*
                 * PHP class names are case-insensitive, which doesn't bother the
                 * Windows filesystem (because of course it doesn't), however,
                 * proper operating systems have case-sensitive file names.
                 * 
                 * This means a simple casing typo in an import can go completely
                 * unnoticed by our Windows-using developers, yet break support
                 * for Linux and macOS, where file_exists() would return false.
                 * 
                 * This is a check for said Windows developers. This compares the
                 * requested class name ($class) with the actual class name.
                 */
                if ( ($a = new \ReflectionClass($class))->name !== $class )
                {
                    // This can also be true for class aliases, so it's important
                    // to be careful here. Class aliases are always defined as
                    // lowercase to the PHP interpreter, so the check is not
                    // performed for class_alias results. Use Rehike\safeClassAlias()
                    // instead.
                    if (strtolower($a->name) === strtolower($class))
                    {
                        // This is 100% a casing issue, report it to the user.
                        throw new Exception(
                            "Class case error loading class $class (unknown case do not trust the error!)"
                        );
                    }
                    else if (
                        self::hasClassAlias($class) && 
                        self::getClassAlias($class) !== $class
                    )
                    {
                        // This is also a known casing issue.
                        throw new Exception(
                            "Class case error loading class $class (unknown case do not trust the error!)"
                        );
                    }
                }
            }
            else
            {
                throw new Exception("Loaded file $path but no class by that name exists");
            }
        }
        else
        {
            throw new Exception("Failed to import class $path");
        }
    }

    /**
     * Registers a known class alias that is safely cased.
     */
    public static function registerClassAlias(string $alias): void
    {
        self::$classAliases[strtolower($alias)] = $alias;
    }

    static function hasClassAlias(string $alias): bool
    {
        return isset(self::$classAliases[strtolower($alias)]);
    }

    static function getClassAlias(string $alias): string
    {
        return self::$classAliases[strtolower($alias)];
    }

    /**
     * Checks if a file exists from the root of the server.
     * 
     * This practically emulates using the include path, which file_exists
     * otherwise does not support.
     * 
     * Additionally, the check is performed case-sensitively, so a non-existent
     * file can be easily detected.
     */
    static function fileExists(string $filename): bool
    {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $filename;

        return 
            \file_exists($path) &&
            \basename(\realpath($path)) === \basename($path)
        ;
    }

    /**
     * Checks if a string starts with a substring.
     */
    static function mapPrefix(
            string $filename, 
            string $prefix,
            string $basedir,
            string &$out
    ): bool
    {
        $len = \strlen($prefix);
        $out = $basedir . substr($filename, $len) . ".php";
        return $prefix == \substr($filename, 0, $len);
    }
}

spl_autoload_register(Autoloader::autoload);
