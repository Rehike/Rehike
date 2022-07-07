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
    
    /**
     * Temporary method for finding the comment section
     * index.
     * 
     * @author YukisCoffee <kirasicecreamm@gmail.com>
     * @param object[] $items array
     * @return void
     */
    public static function findCommentsSection($items)
    {
        for ($i = 0, $j = count($items); $i < $j; $i++)
        {
            if (isset(
                $items[$i]->itemSectionRenderer->sectionIdentifier
            ) &&
                "comment-item-section" == $items[$i]->itemSectionRenderer->sectionIdentifier
            )
            {
                return $items[$i]->itemSectionRenderer;
            }
        }
    }
}