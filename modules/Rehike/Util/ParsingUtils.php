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

    public static function getUrl(mixed $source): ?string
    {
        return @$source->navigationEndpoint->commandMetadata->webCommandMetadata->url
            ?? @$source->navigationEndpoint->confirmDialogEndpoint->content->confirmDialogRenderer->confirmButton->buttonRenderer->command->urlEndpoint->url
            ?? @$source->commandMetadata->webCommandMetadata->url
            ?? null;
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

        // We need to check that the width and height are above 0, because
        // sometimes InnerTube thumbnails have the height set to 0 (for
        // instance, live stream thumbnails in the notifications menu), and
        // that will cause a division by zero error.
        if (
            isset($thumb->width) &&
            isset($thumb->height) &&
            $thumb->width > 0 &&
            $thumb->height > 0
        )
        {
            $ratio = $thumb->width / $thumb->height;

            // 16:9 is 1.777 repeating. The imprecise equation here is done
            // for some edge cases like 1366:768, which is only approximately
            // 16:9 but would still be regarded as such.
            //
            // There is also an isOriginalAspectRatio variable that indicates
            // if the thumbnail is not 16:9 and that the URL is a link to the
            // image in its original aspect ratio (would be stretched).
            $isShort = !($ratio >= 1.7 && $ratio < 1.8) || $container->isOriginalAspectRatio;

            // If the video is a Short, we want to remove the sqp param to 
            // remove any cropping. We also want to switch the oar2 thumb
            // type for hqdefault, since oar2 contains cropping by default.
            // With an ideal i.ytimg.com server, we could use maxresdefault.
            // However, not every thumbnail has a maxresdefault/hq720 variant.
            if ($isShort)
            {
                $url = preg_replace("/\?sqp=.*/", "", $thumb->url);
                return str_replace("oar2", "hqdefault", $url);
            }
            else
            {
                return $thumb->url;
            }
        }
        // If the width or height is 0, just return the URL. It is very likely
        // that the thumbnail is already in the correct format.
        else
        {
            return $thumb->url;
        }

        return null;
    }
}