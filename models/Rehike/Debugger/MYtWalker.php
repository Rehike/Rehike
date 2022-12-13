<?php
namespace Rehike\Model\Rehike\Debugger;

use Rehike\Debugger\Debugger;
use ReflectionObject, ReflectionProperty, ReflectionMethod;

/**
 * Implements the global walker tab.
 * 
 * Note on nomenclature: The first revision of the debugger called the global
 * walker "$yt walker" after the global $yt object. The name of this class
 * still reflects that earlier era.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Developers
 */
class MYtWalker extends MTabContent
{
    public function __construct() {}

    /**
     * JS history should not be allowed on this tab, as it is rendered and
     * controlled by the JS land.
     */
    public $enableJsHistory = false;

    /**
     * Adds a reference to the global data object ($yt).
     * 
     * @param object $yt Global object
     * @return void
     */
    public function addYt($yt)
    {
        $this->richDebuggerRenderer[] = (object)[
            "globalWalkerContainer" => (object)[
                "items" => $yt
            ]
        ];

        Debugger::addContext("jsAttrs", self::getAttrs($yt));
    }

    /**
     * Get the attributes of $yt.
     * 
     * @param object $yt Global object
     * @return array Associative array of additional property information.
     */
    public static function getAttrs($yt)
    {
        $attrs = [];

        self::getAttrsOfObj($attrs, $yt, "yt");

        return $attrs;
    }

    /**
     * Get the attributes of an object and write them to an associative
     * array.
     * 
     * This is used to communicate PHP-specific data outside of PHP while
     * maintaining the use of JSON otherwise. A path is simply provided by
     * the PHP server with additional properties that the client can then parse.
     * 
     * Contrary to the name, this function supports both object and array input.
     * The methods for obtaining metadata differ, so this function has quite a
     * complex body.
     * 
     * TODO: Cleanup.
     * 
     * @param array  $attrs Associative array to write property information to.
     * @param object|array $obj Object to get information from.
     * @param string $path Path to find $obj at (used recursively).
     * 
     * @return void  Writes to $attrs as reference.
     */
    public static function getAttrsOfObj(&$attrs, $obj, $path)
    {
        // Reflection is not used to read arrays, only objects.
        $usingReflection = false;

        /**
         * PATCH (kirasicecreamm): Prevent obvious memory leak from recursion.
         * 
         * The simple solution is to keep a parent stack and check if the
         * requested value is present on it, stopping in our tracks if we need
         * to.
         */
        static $parentStack = [];

        if (is_object($obj))
        {
            /*
             * Objects must be iterated differently from arrays. This is because
             * not all objects are iterable, objects can have hidden properties,
             * and objects can have methods.
             * 
             * In order to use the same code for iterating an array and an
             * object using foreach, a ReflectionObject is created to convert
             * the data to an array that can then be iterated.
             */

            $reflection = new ReflectionObject($obj);

            $source = array_merge(
                $reflection->getProperties(),
                $reflection->getMethods()
            );

            // As reflection is used to read properties, this is set to true
            // for future reference.
            $usingReflection = true;
        }
        else
        {
            // Otherwise it's just an array and it can already be iterated.
            $source = $obj;
        }

        foreach ($source as $key => $value)
        {
            /*
             * Object-specific operations:
             *    - Get the value of a protected or private property.
             *    - Report the original privacy.
             *    - Create a placeholder for method values.
             */
            if ($usingReflection) // object input
            {
                /** 
                 * ReflectionProperty and ReflectionMethod share roughly
                 * the same API, so they can be used interchangeably.
                 * 
                 * However, they don't come from the same base class or
                 * implement a common interface, so they must be regarded as
                 * two separate types.
                 * 
                 * @var ReflectionProperty|ReflectionMethod
                 */
                $reflection = $source[$key];

                $key = $reflection->getName();

                if ($value instanceof ReflectionProperty)
                {
                    /*
                     * PATCH (dcooper): Required before PHP 8.1, else a 
                     * ReflectionException is thrown upon trying to access the 
                     * contents of a protected or private property.
                    */
                    $reflection->setAccessible(true);

                    /**
                     * PATCH (kirasicecreamm): ReflectionProperty::getValue()
                     * will cause a fatal error when attempting to read an
                     * uninitalised *typed* property.
                     */
                    if ($reflection->isInitialized($obj))
                    {
                        $value = $reflection->getValue($obj);
                    }
                    else
                    {
                        $value = "uninitialized";
                    }
                }
                else if ($value instanceof ReflectionMethod)
                {
                    $value = "[function]";
                }

                if ($reflection->isPrivate() || $reflection->isProtected())
                {
                    self::defineAttr($attrs, $path, $key, [
                        "privacy" => $reflection->isPrivate() 
                                        ? "private" 
                                        : "protected"
                    ]);
                }
            }
            else // array input
            {
                $value = $source[$key];
            }

            // Search by type
            if (is_object($value))
            {
                if (false === array_search($value, $parentStack))
                {
                    $parentStack[] = &$value;

                    if ("stdClass" != get_class($value))
                    {
                        self::defineAttr($attrs, $path, $key, [
                            "type" => get_class($value)
                        ]);
                    }

                    // Also deep iterate
                    self::getAttrsOfObj($attrs, $value, "$path.$key");
                }
                else
                {
                    $value = "[ infinite cycle detected ]";
                }
                array_pop($parentStack);
            }
            else if (is_array($value))
            {
                if (false === array_search($value, $parentStack))
                {
                    $parentStack[] = &$value;

                    if (self::isAssociativeArray($value))
                    {
                        self::defineAttr($attrs, $path, $key, [
                            "associativeArray" => true
                        ]);
                    }

                    // Also deep iterate
                    self::getAttrsOfObj(
                        $attrs, $value, "$path.$key", true
                    );
                }
                else
                {
                    $value = "[ infinite cycle detected ]";
                }
                array_pop($parentStack);
            }
            else if (is_callable($value))
            {
                self::defineAttr($attr, $path, $key, [
                    "function" => true
                ]);
            }
        }
    }

    /**
     * Define an attribute definition on the attrs associative array.
     * 
     * @param array  $attrs Associative array of attributes.
     * @param string $path  Path to write to.
     * @param string $key   Key to define.
     * @param mixed  $attr  Value to set.
     */
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

    /**
     * Determine if an array is an associative array.
     * 
     * @param array $arr
     * @return bool
     */
    public static function isAssociativeArray($arr)
    {
        return count(array_filter(array_keys($arr), 'is_string')) > 0;
    }
}