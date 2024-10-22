<?php
namespace Rehike;

class PropertyAtPathException extends \Exception {}

/**
 * PHP lacks traditional syntax for walking property paths.
 * String interpolation may be used for single properties,
 * however children of children may not be accessed.
 * 
 * Or more simply, $a->{'b'} is valid, but you cannot access
 * $a->b->c without multiple pointers.
 * 
 * This is an eval mess, be warned.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class PropertyAtPath
{
    /**
     * Get the PHP pointer string needed
     * for other functions.
     * 
     * @param  object|array $base  Parent object/array,
     * @param  string       $path  JS-style period delimited path
     */
    public static function func(object|array &$base, string $path): string
    {
        $tree = explode(".", $path);

        if (is_object($base))
        {
            $tokenL = "->{'";
            $tokenR = "'}";
        }
        else if (is_array($base))
        {
            $tokenL = "['";
            $tokenR = "']";
        }

        $func = '$base';
        $items = "";
        foreach($tree as $i => $property)
        {
            $arrayCarry = "";

            if (strpos($property, "[") > 0)
            {
                $arrayWorker = explode("[", $property);
                $property = $arrayWorker[0];
                array_splice($arrayWorker, 0, 1);
                $arrayCarry = "[" . implode("[", $arrayWorker);
            }

            $items .= "{$tokenL}{$property}{$tokenR}{$arrayCarry}";
        }
        $func .= $items;

        return $func;
    }

    /**
     * Get a property at a path.
     * 
     * @param  object|array $base  Parent object/array,
     * @param  string       $path  JS-style period delimited path
     */
    public static function get(object|array &$base, string $path): mixed
    {
        $func = self::func($base, $path);
        if (!@eval("return isset({$func});"))
        {
            throw new PropertyAtPathException("Unknown property {$func}");
            return null;
        }
        return @eval("return {$func};");
    }

    /**
     * Set a property at a path.
     * 
     * @param  object|array $base  Parent object/array,
     * @param  string       $path  JS-style period delimited path
     * @param  mixed        $value Value to set the property to.
     */
    public static function set(object|array &$base, string $path, mixed $value): void
    {
        $func = self::func($base, $path);
        @eval("{$func} = \$value;");
    }

    /**
     * Unset a property at a path.
     * 
     * @param  object|array $base  Parent object/array,
     * @param  string       $path  JS-style period delimited path
     */
    public static function unset(object|array &$base, string $path): void
    {
        $func = self::func($base, $path);
        if (!@eval("return isset({$func});"))
        {
            throw new PropertyAtPathException("Unknown property {$func}");
            return;
        }
        @eval("unset({$func});");
    }
}