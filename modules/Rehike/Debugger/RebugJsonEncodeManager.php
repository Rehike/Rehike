<?php
namespace Rehike\Debugger;

use Closure;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

/**
 * Provides an improved JSON serializer for all classes.
 * 
 * This avoids issues with reporting private and protected variables present
 * with the default json_encode method.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class RebugJsonEncodeManager
{
    /**
     * Serialize an object into a JSON string.
     */
    public static function jsonEncode(object $object): string
    {
        return json_encode(self::extractProps($object));
    }
    
    private static function extractProps(object $in): object|string
    {
        static $visitedStack = [];
        
        // If we're already in the stack (i.e. we have a backreference), then just
        // return.
        if (in_array($in, $visitedStack))
        {
            $className = get_class($in);
            $index = array_search($in, $visitedStack);
            
            return "<backreference to object $className at index $index>";
        }
        
        /*
         * Our first step is to optimise stdClass.
         * 
         * We do this even before we bother with pushing to the stack since it
         * isn't a problem with working with unmodified stdClass instances.
         * 
         * Rehike is full of stdClass instances, and there are plenty of cases
         * in which we can avoid even using the shadow.
         * 
         * Because of the nature of stdClass, we cannot have protected or private
         * properties on such objects.
         * 
         * In Rehike, it is rare for our trees of stdClass instances to contain
         * child objects of any type other than stdClass.
         * 
         * We still need to iterate over stdClass in any case, but using a foreach
         * loop should be faster than using reflection, and we don't have the
         * overhead of shadowing to slow us down either.
         * 
         * Note that objects can extend from stdClass and have protected or private
         * properties from there, so we check the class identity strictly rather
         * than using instanceof (which would accept child classes, something we
         * want to avoid).
         */
        if (get_class($in) == stdClass::class)
        {
            $useOptimization = true;
            
            foreach ($in as $key => $value)
            {
                if (is_object($value) && get_class($in) != stdClass::class)
                {
                    $useOptimization = false;
                }
            }
            
            if ($useOptimization)
            {
                return $in;
            }
        }
        
        // Push the current object to the visited stack to avoid recursion loops.
        $visitedStack[] = $in;
        
        /*
         * We make a shadow object, which perfectly mirrors the properties of the
         * input object.
         * 
         * The key difference is that the shadow object makes public clones of
         * all protected and private properties on the input object, maintaining
         * the same names.
         * 
         * Shadowing has significant overhead, even with all of the optimisations
         * we employ like using references whenever possible, so we try to avoid
         * it altogether. Shadowing still requires allocating a new object on the
         * heap with all of the same property names, and that could get pretty
         * heavy when you consider the tens of thousands of objects regularly
         * available in a Rehike instance.
         */
        $shadow = (object)[];
        
        $reflection = new ReflectionObject($in);
        
        /*
         * We'll also optimise shadowing for classes which don't have any private
         * properties, although there's a marginal benefit from doing this.
         */
        $shouldShadow = false;
        
        // First pass: Check if shadowing should be employed:
        foreach ($reflection->getProperties() as $prop)
        {
            if ($prop->isProtected() || $prop->isPrivate())
            {
                $shouldShadow = true;
            }
        }
        
        if (!$shouldShadow)
        {
            array_pop($visitedStack);
            return $in;
        }
        
        // Second pass: employ shadowing if necessary:
        if ($shouldShadow)
        foreach ($reflection->getProperties() as $prop)
        {
            $prop->setAccessible(true);
            
            // The value isn't reset by PHP between iterations, so we must do
            // that ourselves or some semantics may change when the current
            // value of $value is a reference.
            unset($value);
            
            if ($reflection->isUserDefined() && $prop->isInitialized($in))
            {
                // If we're a user class, then we can use a hack to get a
                // reference to the property's value instead of copying it,
                // which is preferable for memory benefits.
                $value = &Closure::bind(function &() use ($prop)
                {
                    return $this->{$prop->getName()};
                }, $in, $in)->__invoke();
            }
            else if ($prop->isInitialized($in))
            {
                // PHP internal classes cannot be bound to a closure, so the
                // above hack would not work out.
                $value = $prop->getValue($in);
            }
            else
            {
                // We will simply report all uninitialised properties as null.
                $value = null;
            }
            
            $name = $prop->getName();
            
            if (is_array($value))
            {
                // This algorithm dealing with arrays could be improved.
                // Currently, it does not handle circular references like objects
                // do. Also, arrays are always shadowed if the owner object is
                // shadowed, which may use more memory than would be ideal.
                
                $shadow->{$name} = [];
                
                foreach ($value as $index => $item)
                {
                    if (is_object($item))
                    {
                        $fixedItem = self::extractProps($item);
                        $shadow->{$name}[$index] = $fixedItem;
                    }
                    else
                    {
                        $shadow->{$name}[$index] = $item;
                    }
                }
            }
            else if (is_object($value))
            {
                $shadow->{$name} = self::extractProps($value);
            }
            else
            {
                $shadow->{$name} = $value;
            }
        }
        
        array_pop($visitedStack);
        
        return $shadow;
    }
}