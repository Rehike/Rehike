<?php
namespace Rehike\Async;

/**
 * Common utilities for the Rehike asynchronous framework.
 * 
 * @static
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
final class Utils
{
    public static function getEnumValueName(string $enumClassName, int $enumValue): string
    {
        foreach ((new \ReflectionClass($enumClassName))->getConstants() as $name => $constant)
        {
            if ($constant == $enumValue)
            {
                return $name;
            }
        }
        
        // This enum value is not stated in the enum definition.
        return (string)$enumValue;
    }
}