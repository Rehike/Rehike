<?php
declare(strict_types=1);
namespace RehikeTool;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

require_once "tool_base.php";

class Linter
{
    private static int $loggerIndentLevel = 0;

    public static function log(string $message): void
    {
        $prefixBuffer = "";
        if (0 != self::$loggerIndentLevel)
        {
            for ($i = 0; $i < self::$loggerIndentLevel * 4; $i++)
            {
                $prefixBuffer .= " ";
            }
            $message = $prefixBuffer . $message;
        }

        // Wrap to 80 characters:
        $tokens = explode(" ", $message);
        $buffer = "";
        $bufferCurLine = "";
        for ($i = 0, $j = count($tokens); $i < $j; $i++)
        {
            // Foresee the length after we add the token:
            if (strlen($bufferCurLine) + strlen($tokens[$i]) > 80)
            {
                $buffer .= $bufferCurLine . "\n$prefixBuffer";
                $bufferCurLine = "";
            }

            $bufferCurLine .= $tokens[$i];

            if ($i != $j)
                $bufferCurLine .= " ";
        }
        $buffer .= $bufferCurLine;
        echo $buffer . PHP_EOL;
    }

    /**
     * Performs runtime linting on the class using the PHP reflection API.
     * 
     * @param class-string $className
     */
    public static function lintClass(string $className): void
    {
        $refCls = new ReflectionClass($className);
        self::log("Class " . $refCls->getName() . ":");
        self::$loggerIndentLevel++;

        foreach ($refCls->getProperties() as $refProp)
        {
            // We want to make sure that properties aren't redefined on
            // subclasses unless their values are changed (even that is a little
            // ugly, but at least there's a legitimate reason to do so)
            $curParent = $refCls->getParentClass();
            while ($curParent)
            {
                try
                {
                    if ($refParentProp = $curParent->getProperty($refProp->getName()))
                    {
                        if ($refProp->getDeclaringClass() != $refParentProp->getDeclaringClass())
                        {
                            self::log("Property redeclared from parent: " . (string)$refProp);

                            // If the value differs, but is not blank, then we'll
                            // accept it (for now)
                            $ourValue = $refProp->getDefaultValue();
                            $theirValue = $refParentProp->getDefaultValue();
                            if (!$refProp->hasDefaultValue())
                            {
                                self::log(
                                    "Property \"" . $refProp->getName() .
                                    "\" is redeclared without a default value in class \"" .
                                    $refCls->getName() . "\" from parent \"" .
                                    $curParent->getName() . "\"."
                                );
                            }
                            else if ($ourValue !== $theirValue)
                            {
                                self::log(
                                    "Property \"" . $refProp->getName() .
                                    "\" is duplicated between class \"" .
                                    $refCls->getName() . "\" and parent \"" .
                                    $curParent->getName() . "\"."
                                );
                            }

                            if ($refProp->getType() != $refParentProp->getType())
                            {
                                self::log(
                                    "Property \"" . $refProp->getName() .
                                    "\", redeclared in class \"" .
                                    $refCls->getName() . "\" from parent \"" .
                                    $curParent->getName() . "\", has a different " .
                                    "type from its parent."
                                );
                            }
                        }
                    }
                }
                catch (ReflectionException $e)
                {
                }

                $curParent = $curParent->getParentClass();
            }

            // We want to ensure that all class members are typed unless they
            // have a doc comment stating "@type resource" or "@type callable"
            // (illegal class member types)
            $refType = null;
            if (($refType = $refProp->getType())
                || self::hasDocCommentType($refProp, 
                    ["resource", "callable"]))
            {
                // This is the good case.
            }
            else
            {
                self::log(
                    "Property \"" . $refProp->getDeclaringClass()->getName() . "::" .
                    $refProp->getName() . "\" lacks a " .
                    "valid type annotation."
                );
            }
        }

        self::$loggerIndentLevel--;
    }

    private static function hasDocCommentType(
        ReflectionProperty $refProp,
        ?array $filterList = null,
    ): bool
    {
        $docComment = $refProp->getDocComment();

        if (false === $docComment)
        {
            // No doc comment.
            return false;
        }

        $status = preg_match("/@type\s+(\w)/", $docComment, $matches);
        $type = @$matches[1];
        if (!$status || !$type)
        {
            // No type matched.
            return false;
        }

        if (null === $filterList)
        {
            // At this point, we know we have a return. If we're not filtering,
            // then we're good to go.
            return true;
        }
        else
        {
            foreach ($filterList as $filter)
            {
                if ($type == $filter)
                {
                    return true;
                }
            }
        }

        // We'll hit this point if we have a filter list set and didn't match
        // any of the filter values.
        return false;
    }
}

// For now, we don't do any command line argument parsing. Instead, I will just
// supply a hardcoded class name:
Linter::lintClass(\Rehike\Model\Footer\MPickerSafetyButton::class);
Linter::lintClass(\Rehike\Model\Common\Subscription\MSubscriptionPreferencesButton::class);