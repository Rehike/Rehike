<?php
namespace Rehike\Model\Masthead\Pony;

/**
 * Manages the My Little Pony easter egg which appears when you search the name
 * of any character from the series Friendship is Magic.
 * 
 * This is based on an actual YouTube easter egg from 2013, although Rehike
 * expands it a little bit.
 * 
 * @see https://mlpforums.com/topic/69644-youtube-ponies-colors/
 * @see https://www.equestriadaily.com/2013/08/youtube-geek-week-is-over-ponies.html
 * @see https://www.youtube.com/watch?v=sMs-TGmfZNw    Video showing it off in action
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PonyEasterEggManager
{
    /**
     * Determines if there is a pony related to the given search query, and
     * returns a pony API if there is one.
     */
    public static function getPonyForSearchQuery(string $rawQuery): ?Pony
    {
        $query = strtolower($rawQuery);
        
        $isDirectlyMlpRelated =
            strpos($query, "my little pony") !== false ||
            strpos($query, "mlp") !== false ||
            strpos($query, "friendship is magic") !== false ||
            strpos($query, "fim") !== false ||
            strpos($query, "equestria girls") !== false ||
            strpos($query, "eqg") !== false ||
            strpos($query, "eg") !== false ||
            strpos($query, "pony") !== false;
        
        // Pony match flags (bitmask)
        $PMF_CONTAINS           = 0b0000;
        $PMF_DIRECTLY_RELATED   = 0b0001;
        $PMF_SEARCH_FOR_KEYWORD = 0b0010;
        
        // Match helper function:
        $hasPony = function(string $name, int $flags = /* $PMF_CONTAINS */ 0, mixed $modeSpecificInfo = null)
                use ($query, $isDirectlyMlpRelated, $PMF_CONTAINS, $PMF_DIRECTLY_RELATED, 
                     $PMF_SEARCH_FOR_KEYWORD): bool
        {
            $searchForKeywordSucceeded = false;
            
            if ($flags & $PMF_SEARCH_FOR_KEYWORD)
            {
                // $modeSpecificInfo will be a comma-delinated string of keywords
                // to search for.
                if (is_array($modeSpecificInfo) && isset($modeSpecificInfo["keywords"]))
                {
                    $keywords = explode(",", $modeSpecificInfo["keywords"]);
                    
                    foreach ($keywords as $keyword)
                    {
                        if (strpos($query, $keyword) !== false)
                        {
                            $searchForKeywordSucceeded = true;
                            break;
                        }
                    }
                }
            }
            
            if (
                ($flags == $PMF_CONTAINS) ||
                ($flags & $PMF_DIRECTLY_RELATED && $isDirectlyMlpRelated) ||
                ($flags & $PMF_SEARCH_FOR_KEYWORD && $searchForKeywordSucceeded)
            )
            {
                return strpos($query, $name) !== false;
            }
            
            return false;
        };
        
        $pony = null;
        
        $pony = match (true)
        {
            $hasPony("twilight sparkle") => new Pony(Pony::TWILIGHT_SPARKLE),
            // variant
            $hasPony("twilight", $PMF_DIRECTLY_RELATED) => new Pony(Pony::TWILIGHT_SPARKLE),
            $hasPony("pinkie pie") => new Pony(Pony::PINKIE_PIE),
            $hasPony("applejack") => new Pony(Pony::APPLEJACK),
            // variant
            $hasPony("apple jack") => new Pony(Pony::APPLEJACK),
            $hasPony("rarity") => new Pony(Pony::RARITY),
            $hasPony("rainbow dash") => new Pony(Pony::RAINBOW_DASH),
            $hasPony("fluttershy") => new Pony(Pony::FLUTTERSHY),
            
            $hasPony("derpy") => new Pony(Pony::DERPY),
            $hasPony("derpy hooves") => new Pony(Pony::DERPY),
            // variant name
            $hasPony("ditzy doo") => new Pony(Pony::DERPY),
            // variant name
            $hasPony("muffins", $PMF_DIRECTLY_RELATED) => new Pony(Pony::DERPY),
            
            $hasPony("princess celestia") => new Pony(Pony::CELESTIA),
            // variant
            $hasPony("celestia", $PMF_DIRECTLY_RELATED) => new Pony(Pony::CELESTIA),
            $hasPony("princess luna") => new Pony(Pony::LUNA),
            $hasPony("luna", $PMF_DIRECTLY_RELATED) => new Pony(Pony::LUNA),
            $hasPony("princess cadance") => new Pony(Pony::CADANCE),
            // common misspelling
            $hasPony("princess cadence") => new Pony(Pony::CADANCE),
            $hasPony("cadance", $PMF_DIRECTLY_RELATED) => new Pony(Pony::CADANCE),
            // common misspelling
            $hasPony("cadence", $PMF_DIRECTLY_RELATED) => new Pony(Pony::CADANCE),
            $hasPony("shining armor") => new Pony(Pony::SHINING_ARMOR),
            // possibly common british misspelling of proper name
            $hasPony("shining armour") => new Pony(Pony::SHINING_ARMOR),
            
            $hasPony("scootaloo") => new Pony(Pony::SCOOTALOO),
            $hasPony("apple bloom") => new Pony(Pony::APPLEBLOOM),
            // variant
            $hasPony("applebloom") => new Pony(Pony::APPLEBLOOM),
            $hasPony("sweetie belle") => new Pony(Pony::SWEETIE_BELLE),
            
            // older (and still common) variant
            $hasPony("big macintosh") => new Pony(Pony::BIG_MCINTOSH),
            // revised variant
            $hasPony("big mcintosh") => new Pony(Pony::BIG_MCINTOSH),
            $hasPony("big mac", $PMF_DIRECTLY_RELATED) => new Pony(Pony::BIG_MCINTOSH),
            
            $hasPony("babs seed") => new Pony(Pony::BABS_SEED),
            
            /*
             * The name Discord is way more frequently used to refer to the popular
             * chatting application nowadays, such that you can only find results
             * related the MLP character by explicitly including a keyword for MLP.
             * 
             * As a result, we always specify that this match must be directly
             * related to MLP.
             * 
             * (btw discord is very dubiously a pony lol)
             */
            $hasPony("discord", $PMF_DIRECTLY_RELATED) => new Pony(Pony::DISCORD),
            
            $hasPony("king sombra") => new Pony(Pony::SOMBRA),
            $hasPony("queen chrysalis") => new Pony(Pony::CHRYSALIS),
            $hasPony("nightmare moon") => new Pony(Pony::NIGHTMARE_MOON),
            
            /*
             * Spike is a common word in the English language, and searching it on
             * YouTube gives you a lot of nonfocused results, but there are two
             * ways to really refer to Spike from MLP:
             * 
             * - "Spike [from] MLP"  "spike mlp"
             * - "Spike the Dragon"  "spike the dragon"
             * 
             * I decided to not include "Spike the Dog" (Equestria Girls), because
             * it doesn't result in focused results, particularly because it can
             * also refer commonly to Spike, the dog from Tom and Jerry.
             */
            $hasPony("spike", $PMF_DIRECTLY_RELATED | $PMF_SEARCH_FOR_KEYWORD, [
                "keywords" => "dragon"
            ]) => new Pony(Pony::SPIKE),
            
            //===================================================================================
            // Common background ponies as of 2013:
            //
            
            // Also captures full name "Trixie Lulamoon"
            $hasPony("trixie") => new Pony(Pony::TRIXIE),
            
            // Also captures full name "Lyra Heartstrings"
            $hasPony("lyra") => new Pony(Pony::LYRA),
            $hasPony("bonbon", $PMF_DIRECTLY_RELATED) => new Pony(Pony::BONBON),
            // variant
            $hasPony("bon bon", $PMF_DIRECTLY_RELATED) => new Pony(Pony::BONBON),
            // variant name
            $hasPony("sweetie drops") => new Pony(Pony::BONBON),
            
            $hasPony("vinyl scratch") => new Pony(Pony::VINYL_SCRATCH),
            
            // variant name
            $hasPony("dj pon3") => new Pony(Pony::VINYL_SCRATCH),
            $hasPony("dj pon-3") => new Pony(Pony::VINYL_SCRATCH),
            
            /*
             * Variant direct-relation distinction is used for Octavia because the
             * name alone more commonly refers to the Helluva Boss character.
             */
            $hasPony("octavia melody") => new Pony(Pony::OCTAVIA),
            // variant
            $hasPony("octavia", $PMF_DIRECTLY_RELATED) => new Pony(Pony::OCTAVIA),
            
            // Dr Hooves has 6 different spelling variations:
            $hasPony("dr. hooves") => new Pony(Pony::DR_HOOVES),
            $hasPony("dr hooves") => new Pony(Pony::DR_HOOVES),
            $hasPony("doctor hooves") => new Pony(Pony::DR_HOOVES),
            $hasPony("dr. whooves") => new Pony(Pony::DR_HOOVES),
            $hasPony("dr whooves") => new Pony(Pony::DR_HOOVES),
            $hasPony("doctor whooves") => new Pony(Pony::DR_HOOVES),
            
            $hasPony("zecora") => new Pony(Pony::ZECORA),
            $hasPony("dinky doo") => new Pony(Pony::DINKY_DOO),
            $hasPony("daring do") => new Pony(Pony::DARING_DO),
            
            //===================================================================================
            // New entries since 2013:
            //
            
            $hasPony("starlight glimmer") => new Pony(Pony::STARLIGHT),
            // variant
            $hasPony("starlight", $PMF_DIRECTLY_RELATED) => new Pony(Pony::STARLIGHT),
            
            default => null
        };
        
        return $pony;
    }
}