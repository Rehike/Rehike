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
    if (isset($guideItem->entryData->guideEntryData->guideEntryId))
    {
        return $guideItem->entryData->guideEntryData->guideEntryId;
    }
    else if (isset($guideItem->navigationEndpoint->browseEndpoint->browseId))
    {
        return $guideItem->navigationEndpoint->browseEndpoint->browseId;
    }
    else
    {
        return "";
    }
});