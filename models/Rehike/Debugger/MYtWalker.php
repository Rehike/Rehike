<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\Debugger\Debugger;
use ReflectionObject, ReflectionProperty;

/**
 * Implements the $yt walker tab.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MYtWalker extends MTabContent
{
    public function __construct() {}

    public $enableJsHistory = false;

    public function addYt($yt)
    {
        $this->richDebuggerRenderer[] = (object)[
            "globalWalkerContainer" => (object)[
                "items" => $yt
            ]
        ];

        Debugger::addContext("jsAttrs", self::getAttrs($yt));
    }

    public static function getAttrs($yt)
    {
        $attrs = [];

        self::getAttrsOfObj($attrs, $yt, "yt");

        return $attrs;
    }

    public static function getAttrsOfObj(&$attrs, $obj, $path)
    {
        // Objects need to be iterated differently.
        $usingReflection = false;
        if (is_object($obj))
        {
            $reflection = new ReflectionObject($obj);
            $source = array_merge(
                $reflection->getProperties(),
                $reflection->getMethods()
            );

            $usingReflection = true;
        }
        else
        {
            $source = $obj;
        }

        // ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();ob_end_clean();
        // var_dump($reflection->getProperties());
        // die();

        foreach ($source as $key => $value)
        {
            if ($usingReflection)
            {
                /** @var ReflectionProperty */
                $reflection = $source[$key];

                $key = $reflection->getName();

                if ($value instanceof ReflectionProperty)
                {
                    /*
                     * PATCH (dcooper): Required before PHP 8.1, else a ReflectionException is thrown
                     * upon trying to access the contents of a protected or private property.
                    */
                    $reflection->setAccessible(true);

                    $value = $reflection->getValue($obj);
                }
                else
                {
                    $value = "[function]";
                }

                if ($reflection->isPrivate() || $reflection->isProtected())
                {
                    self::defineAttr($attrs, $path, $key, [
                        "privacy" => $reflection->isPrivate() ? "private" : "protected"
                    ]);
                }
            }
            else
            {
                $value = $source[$key];
            }

            // Search by type
            if (is_object($value))
            {
                if ("stdClass" != get_class($value))
                {
                    self::defineAttr($attrs, $path, $key, [
                        "type" => get_class($value)
                    ]);
                }

                // Also deep iterate
                self::getAttrsOfObj($attrs, $value, "$path.$key");
            }
            else if (is_array($value))
            {
                if (self::isAssociativeArray($value))
                {
                    self::defineAttr($attrs, $path, $key, [
                        "associativeArray" => true
                    ]);
                }

                // Also deep iterate
                self::getAttrsOfObj($attrs, $value, "$path.$key");
            }
            else if (is_callable($value))
            {
                self::defineAttr($attr, $path, $key, [
                    "function" => true
                ]);
            }
        }
    }

    public static function defineAttr(&$attrs, $path, $key, $attr)
    {
        if (!isset($attrs["$path.$key"]))
        {
            $attrs["$path.$key"] = $attr;
        }
        else
        {
            $attrs["$path.$key"] += $attr;
        }
    }

    public static function isAssociativeArray($arr)
    {
        return count(array_filter(array_keys($arr), 'is_string')) > 0;
    }
}