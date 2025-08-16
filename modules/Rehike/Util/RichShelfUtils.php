<?php
namespace Rehike\Util;

/**
 * General utilities for converting richShelfRenderers from InnerTube into
 * standard shelf renderers.
 * 
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * @author The Rehike Maintainers
 */
class RichShelfUtils
{
    /**
     * Reformat a base response.
     */
    public static function reformatResponse(object $response): object
    {
        if (!isset($response->onResponseReceivedActions)) return $response;

        $contents = [];
        foreach ($response->onResponseReceivedActions as $action)
        if (isset($action->appendContinuationItemsAction->continuationItems)) 
        foreach ($action->appendContinuationItemsAction->continuationItems as $item)
        if (isset($item->richSectionRenderer->content->richShelfRenderer))
        {
            $contents[] = self::reformatShelf($item);
        }
        else
        {
            $contents[] = $item;
        }
        
        return (object) [
            "sectionListRenderer" => (object) [
                "contents" => $contents
            ]
        ];
    }

    /**
     * Convert a richShelfRenderer into a standard shelfRenderer (as well as
     * any outer wrappers).
     * 
     * @param object $shelf The iteration of shelf to use.=
     * @param bool $list Format in list form?
     * @return object Modified shelf.
     */
    public static function reformatShelf(object $shelf, bool $list = false): object
    {
        if (!isset($shelf->richSectionRenderer->content->richShelfRenderer)) return $shelf;

        $richShelf = $shelf->richSectionRenderer->content->richShelfRenderer;
        $response = (object) [];
        $response->title = $richShelf->title ?? null;
        $response->titleAnnotation = $richShelf->subtitle ?? null;
        $response->thumbnail = $richShelf->thumbnail ?? null;
        $response->endpoint = $richShelf->endpoint ?? null;
        $response->menu = $richShelf->menu ?? null;
        $contents = [];

        foreach($richShelf->contents as $item)
            $contents[] = self::reformatShelfItem($item, $list);

        $response->content = (object) [
            "horizontalListRenderer" => (object) [
                "items" => $contents
            ]
        ];

        return (object) [
            "itemSectionRenderer" => (object) [
                "contents" => [
                    (object) [
                        "shelfRenderer" => $response
                    ]
                ]
            ]
        ];

        return $shelf;
    }

    /**
     * Used to extract richItemRenderers used within rich shelves.
     * 
     * @param object $item richItemRenderer
     * @param bool $list Format in list form?
     * @return object $richItemRenderer->content
     */
    public static function reformatShelfItem(object $item, bool $list = false): ?object
    {
        if (isset($item->richItemRenderer->content))
        {
            foreach ($item->richItemRenderer->content as $key => $val)
            {
                // If we get a lockup view model, then we don't want to prepend "grid" because it will
                // mess up the converter.
                if (!$list && $key != "lockupViewModel") $key = "grid" . ucfirst($key);
                return (object) [
                    $key => $val
                ];
            }
        }
        else
        {
            return $item;
        }
        
        return null;
    }
}