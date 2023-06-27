<?php
namespace YukisCoffee\CoffeeRequest\Helper;

use YukisCoffee\CoffeeRequest\Exception\MethodPrivacyException;

use ReflectionClass;
use ReflectionMethod;

/**
 * Implements common utilities to the singleton classes.
 * 
 * This better allows code reuse within the classes internally,
 * as it doesn't override method names on child classes.
 * 
 * @template T
 * @internal
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
final class SingletonUtils/*<T>*/
{
    /** Utils class should not be constructable! */
    private function __construct() {}

    /**
     * A public class member. This is the default.
     */
    public const PRIVACY_PUBLIC = 0;

    /**
     * A protected class member.
     */
    public const PRIVACY_PROTECTED = 1;

    /**
     * A private class member.
     */
    public const PRIVACY_PRIVATE = 2;

    /**
     * Call a method on an instance.
     * 
     * @param string<T> $class Type name of the class.
     * @param T $instance
     * @param mixed[] $args
     * @return mixed
     */
    public static function call(
            /*string<T>*/ $class,
            /*T*/ $instance,
            string $name,
            array $args,
            bool $static = false
    )
    {
        $classRef = new ReflectionClass($instance);
        $sameClass = ($instance::class == $class);

        $method = $classRef->getMethod($name);

        switch (self::getMethodPrivacy($method))
        {
            case self::PRIVACY_PRIVATE;
                if (!$sameClass)
                {
                    throw new MethodPrivacyException(
                        "Call to private method $class::$name()"
                    );
                }
                // Do not break
            case self::PRIVACY_PROTECTED:
                if (!($instance instanceof $class))
                {
                    throw new MethodPrivacyException(
                        "Call to protected method $class::$name()"
                    );
                }

                // Both private and protected members require
                // this prior to PHP 8.0, but no check is required
                $method->setAccessible(true);
        }
        
        if ($static && $method->isStatic())
        {
            $method->invoke(null, $args);
        }
        else
        {
            $method->invoke($instance, $args);
        }
    }

    /**
     * Get the privacy of a method.
     */
    public static function getMethodPrivacy(ReflectionMethod $m): int
    {
        switch (true)
        {
            case $m->isPrivate():   return self::PRIVACY_PRIVATE;
            case $m->isProtected(): return self::PRIVACY_PROTECTED;
            default:                return self::PRIVACY_PUBLIC;
        }
    }
}