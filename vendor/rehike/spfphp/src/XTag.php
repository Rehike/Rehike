<?php
namespace SpfPhp;

/**
 * Implements helpers for the x-tag functionality of SpfPhp.
 * 
 * X-tags are unique attributes that tell SpfPhp what to do. They are
 * used just like any other attribute in HTML, but they have unique
 * behaviour.
 * 
 * @author      The Rehike Maintainers
 * @license     MIT
 */
class XTag
{
    const XTAG_PREFIX = "x-spfphp-";

    /**
     * Split an array of attributes (a string array), putting the x-tags
     * in a separate array.
     * 
     * @param string[] $attributes
     * @param array $xtags
     * @return void as the xtags are set through the reference argument.
     */
    public static function splitAttributesArray(&$attributes, &$xtags)
    {
        $xtags = [];
        
        if (null != $attributes) foreach ($attributes as $name => $value)
        {
            if (($x = self::XTAG_PREFIX) == substr($name, 0, strlen($x)))
            {
                // Remove the prefix from the name
                $newName = substr($name, strlen($x));

                // Add the item to the xtags array
                $xtags += [$newName => $value];

                // Remove the item from the original array
                unset($attributes[$name]);
            }
        }
    }

    /**
     * Systematically remove them, like any kind of termite or roach...
     * 
     * @param string $payload of any type, though most probably HTML.
     * @return string of the same payload, but without x-tags.
     */
    public static function erradicate($payload)
    {
        $xtag = self::XTAG_PREFIX;

        // What is this mess?
        // Well, it's none other than the classics of PHP Heredoc.
        // You cannot indent it at all without that indentation being
        // included in the output, so it looks ugly whenever it may be
        // used in code.
        //
        // Anyways, this is a beast of a regexp that selects any and all
        // attributes with the x-tag in them.
        // The define statement at the top ("attribute") defines a pattern
        // for selecting any string enclosed in quotes, i.e. the value of
        // an attribute.
        // The below statement controls most of the actual selection. Except
        // that `${xtag}` is PHP string interpolation, it uses the variable
        // declared above.
        //
        // Finally, this is not global (/g) because preg_replace does not like
        // that modifier. Reusers should be aware.
        //
        // See the inline comments for more information:
        $re = <<< EOL
/(?(DEFINE)
    (?'attribute'
        ["|'].*?(?=["|'])["|']
    )
)

\s?${xtag}[a-zA-Z-]*(
    (?# 
        Optionally check if there is whitespace, and the following character
        is an = sign, as to prevent unwanted selection of any succeeding attribute:
    )
        \s?(?=\=)?
    
    (?# Select the = sign if it's there: )
        =?

    (?# Ditto above, lookbehind: )
        (?<=\=)\s?

    (?# 
        Select the attribute or all characters until the next whitespace
        character or >, HTML tag close character:
    )
        ((?P>attribute))? | .*?(?=\s|\>)
)/x
EOL;

        // Use that above regexp to remove all x-tags from here.
        return preg_replace($re, "", $payload);
    }
}