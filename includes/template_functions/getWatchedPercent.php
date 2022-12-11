<?php

/**
 * Helper function for finding the Watch Later button when building
 * HTML templates.
 * 
 * This iterates the overlays array and searches for how much of the
 * video has been watched. If it's not present, this will return null.
 * 
 * This is meant to be used as a helper function for Twig.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Aubrey Pankow <aubyomori@gmail.com>
 * 
 * @param object $array of the thumbnail overlays
 * 
 * @return ?object
 */
\Rehike\TemplateFunctions::register("getWatchedPercent", function($array) {
    if (!isset($array -> thumbnailOverlays )) return null;

    foreach ($array -> thumbnailOverlays as $index => $contents) {
        if (isset($contents -> thumbnailOverlayResumePlaybackRenderer)) {
            return $contents -> thumbnailOverlayResumePlaybackRenderer -> percentDurationWatched;
        }
    }
    
    // Return null if the index doesn't exist.
    return null;
});