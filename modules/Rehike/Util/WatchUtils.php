<?php
namespace Rehike\Util;

/**
 * Common watch utilities for Rehike.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class WatchUtils
{
    /**
     * Get the index of the first autoplay video from a set of recommendations.
     * 
     * The autoplay video must be the first compactVideoRenderer, or regular
     * video, that can be accessed in the list. It cannot be a playlist type.
     * 
     * @param object[] $results Recommendations sidebar.
     * @param int      $index   Index to return as well (modified by reference).
     */
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