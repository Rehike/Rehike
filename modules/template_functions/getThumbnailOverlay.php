<?php

/**
 * Helper function for finding thumbnail overlays.
 * 
 * This iterates the overlays array and searches for the
 * provided identifier.
 * 
 * This is meant to be used as a helper function for Twig.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * 
 * @param object $array of the thumbnail overlays
 * @param string $name of the overlay identifier
 * 
 * @return ?object
 */
\Rehike\TemplateFunctions::register("getThumbnailOverlay", function($array, $name)
{
    if (!isset($array -> thumbnailOverlays )) return null;

    // Iterate the array and figure out the thumbnail overlay
    foreach ($array -> thumbnailOverlays as $index => $contents)
    {
        // InnerTube API formats thumbnail overlays as
        // keys within an object. Fortunately, this is pretty
        // easy to check within PHP.
        if (isset($contents -> $name)) return $contents -> $name;
    }
    
    // Return null if the index doesn't exist.
    return null;
});