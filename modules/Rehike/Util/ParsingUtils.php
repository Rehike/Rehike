<?php
namespace Rehike\Util;

use function count;
use function array_key_last;
use function preg_replace;

/**
 * Provides common InnerTube parsing utilities for Rehike.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ParsingUtils
{
    /**
     * Get the text content of an InnerTube API response field.
     */
    public static function getText(mixed $source): ?string
    {
        // Determine the source type and act accordingly.
        if (is_object($source))
        {
            /*
             * InnerTube has two main text types:
             * 
             *   - runs: Fragments of formatted text separated into an
             *           array for further parsing. Runs should typically
             *           be parsed contextually or with a different method
             *           during templating, rather than by this function.
             * 
             *   - simpleText: Unformatted raw strings.
             * 
             * Rarely, these are even used interchangeably. It just helps
             * to have a single function that can handle both cases and
             * return a single, unformatted string.
             */
            if (isset($source->runs))
            {
                $response = "";

                foreach ($source->runs as $run)
                {
                    $response .= $run->text;
                }

                return $response;
            }
            else if (isset($source->simpleText))
            {
                return $source->simpleText;
            }
        }
        else if (is_string($source))
        {
            return $source;
        }
        
        // If no text is found, return null so that further code can
        // handle error cases.
        return null;
    }

    /**
     * Get the thumbnail of an InnerTube API response field.
     * 
     * This will work on anything from video renderers to avatar images
     * for users.
     */
    public static function getThumb(object $container, int $height = 0): ?string
    {
        // We no longer support other access methods.
        if (!isset($container->thumbnails)) return null;

        $thumbs = &$container->thumbnails;
        $thumb = null;
        foreach ($thumbs as $ithumb)
        {
            if (isset($ithumb->height) && $ithumb->height >= $height)
            {
                $thumb = $ithumb;
            }
        }

        if (is_null($thumb))
        {
            // If there's no height specified, then use the largest one.
            $thumb = $thumbs[array_key_last($thumbs)];
        }

        if (
            isset($thumb->width) &&
            isset($thumb->height) &&
            $thumb->width > 0 &&
            $thumb->height > 0
        )
        {
            $ratio = $thumb->width / $thumb->height;

            // 16:9 is 1.777 repeating. I believe the imprecise equation
            // here is done for some edge cases like 1366:768, which is
            // only approximately 16:9 but would still be regarded as such.
            if ($ratio >= 1.7 && $ratio < 1.8)
            {
                return $thumb->url;
            }
            else
            {
                // In the case that the thumbnail isn't 16:9, it's
                // probably a Short. In this case, the sqp parameter
                // should be removed from the thumbnail.
                return preg_replace("/\?sqp=.*/", "", $thumb->url);
            }
        }
    }
}