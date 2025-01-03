<?php
namespace Rehike\Player;

use ReflectionObject;

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
     */
    public string $baseJsUrl;

    /**
     * The URL of the player's base stylesheet, required
     * in order for it to display properly in a HTML document.
     */
    public string $baseCssUrl;

    /**
     * A valued used to protect video streams. This is required for
     * playback on the client.
     */
    public int $signatureTimestamp;

    /**
     * The URL of the player's embed JS module.
     */
    public string $embedJsUrl;

    public string $latestJsUrl;
    public string $latestCssUrl;

    /**
     * PHP does not have native object casting (whyyyyyyyyy)
     * 
     * I stole most of this from a Stack Overflow function:
     * https://stackoverflow.com/a/9812023
     */
    public static function from(object $obj): PlayerInfo
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