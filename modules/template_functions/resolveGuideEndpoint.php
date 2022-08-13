<?php

/**
 * Resolve a guide endpoint (used for some attributes on guide items, like the IDs).
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 * 
 * @param object $guideItem
 * @return string
 */
\Rehike\TemplateFunctions::register('resolveGuideEndpoint', function($guideItem) {
    // $guideItem = guideEntryRenderer
    if (isset($guideItem->navigationEndpoint->browseEndpoint->browseId))
    {
        $id = $guideItem->navigationEndpoint->browseEndpoint->browseId;
        
        // Remove FE substring if present
        if ("FE" == substr($id, 0, 2))
        {
            $id = substr($id, 2);
        }

        return $id;
    }
    else
    {
        return "";
    }
});