<?php

/**
 * Helper function for finding the Watch Later button when building
 * HTML templates.
 * 
 * This iterates the overlays array and searches for the Watch
 * Later button. If it's not present, this will return null.
 * 
 * This is meant to be used as a helper function for Twig.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * 
 * @param object $array of the thumbnail overlays
 * 
 * @return ?object
 */
\Rehike\TemplateFunctions::register("getWLOverlay", function($array)
{
    if (!isset($array->thumbnailOverlays )) return null;

    foreach ($array->thumbnailOverlays as $index => $contents)
    {
        if (isset($contents->thumbnailOverlayToggleButtonRenderer) &&
            "WATCH_LATER" == @$contents->thumbnailOverlayToggleButtonRenderer 
           ->untoggledIcon->iconType
        )
        {
            return $contents->thumbnailOverlayToggleButtonRenderer;
        }
    }
    
    // Return null if the index doesn't exist.
    return null;
});