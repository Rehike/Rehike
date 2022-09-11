<?php
namespace Rehike\Player;

use Reflectionobject;

/**
 * Implements the player information schema.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class PlayerInfo
{
    /**
     * The URL of the player's base JS module.
     * 
     * @var string
     */
    public $baseJsUrl;

    /**
     * The URL of the player's base stylesheet, required
     * in order for it to display properly in a HTML document.
     * 
     * @var string
     */
    public $baseCssUrl;

    /**
     * A valued used to protect video streams. This is required for
     * playback on the client.
     * 
     * @var int
     */
    public $signatureTimestamp;

    /**
     * PHP does not have native object casting (whyyyyyyyyy)
     * 
     * I stole most of this from a Stack Overflow function:
     * https://stackoverflow.com/a/9812023
     * 
     * @param object $obj
     * @return PlayerInfo
     */
    public static function from($obj)
    {
        $casted = new self();

        $sourceReflection = new ReflectionObject($obj);
        $destinationReflection = new ReflectionObject($casted);

        $sourceProps = $sourceReflection->getProperties();
        foreach ($sourceProps as $prop)
        {
            $prop->setAccessible(true);
            $name = $prop->getName();
            $value = $prop->getValue($obj);

            if ($destinationReflection->hasProperty($name))
            {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($casted,$value);
            }
            else
            {
                $casted->$name = $value;
            }
        }

        return $casted;
    }
}