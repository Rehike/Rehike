<?php
namespace YukisCoffee; // Vendor syntax

class GetPropertyAtPathException extends CoffeeException {}

function getPropertyAtPath(&$base, $propertyPath) 
{
    // PHP lacks traditional syntax for walking property paths.
    // String interpolation may be used for single properties,
    // however children of children may not be accessed.
    //
    // Or more simply, $a->{'b'} is valid, but you cannot access
    // $a->b->c without multiple pointers.
    //
    // This is an eval mess, be warned.
    //
    // $base: parent object or array
    // $propertyPath: JS-style a.b.c path from parent
    // Example: getPropertyAtPath($a, "b.c")
    //
    // coffee <3

    $tree = explode(".", $propertyPath);

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
    else
    {
        throw new GetPropertyAtPathException("Argument 0 must of be of type Object|Array");
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
        if (!@eval("return isset(\$base{$items});"))
        {
            throw new GetPropertyAtPathException(
                "Unknown property \$parent{$items}"
            );
        }
    }
    $func .= $items;
    $func .= ";";

    return @eval("return {$func};");
}