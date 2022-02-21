<?php
class WatchUtils
{
    public static function getRecomAutoplay($results, &$index = 0)
    {
        // Splice first video from results and return an index.
        
        for ($i = 0; $i < count($results); $i++) if (isset($results[$i]->compactVideoRenderer))
        {
            $index = $i;
            return $results[$i];
        }
    }
}