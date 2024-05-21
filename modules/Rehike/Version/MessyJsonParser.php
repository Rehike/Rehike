<?php
namespace Rehike\Version;

/**
 * A permissive JSON parser which can parse JSON with a few errors.
 * 
 * As it turns out, the script responsible for updating the .version file
 * can produce invalid output, especially when double quote literals are
 * used in the commit message or body.
 * 
 * @author Isabella <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class MessyJsonParser
{
    /**
     * Parse a "messy" JSON file.
     */
    public static function parse(string $json): ?object
    {
        $transformed = self::quoteTransform($json);
        
        if ($result = json_decode($transformed))
        {
            return $result;
        }
        
        return null;
    }
    
    /**
     * Ignore unescaped double quotes within lines.
     */
    private static function quoteTransform(string $json): string
    {
        $lines = explode("\n", $json);
        $outLines = [];
        
        foreach ($lines as $line)
        {
            $pair = explode(":", $line);
            $key = @$pair[0];
            $value = @$pair[1];
            
            $numberOfQuotes = substr_count($value, '"');
            $fixedQuotes = $value;
            
            if ($numberOfQuotes > 2)
            {
                for ($i = 0, $offset = strpos($value, '"'); $i < $numberOfQuotes - 1; $i++)
                {
                    if ($i != 0 && $i != $numberOfQuotes - 1)
                        $fixedQuotes = substr_replace($fixedQuotes, "\\", $offset, 0);
                    
                    $offset = strpos($fixedQuotes, '"', $offset + 2);
                }
                
                $fixedPair = implode(":", [$key, $fixedQuotes]);
            }
            else
            {
                $fixedPair = $line;
            }
            
            $outLines[] = $fixedPair;
        }
        
        return implode("\n", $outLines);
    }
}