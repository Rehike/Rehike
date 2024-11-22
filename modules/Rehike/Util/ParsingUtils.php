<?php
namespace Rehike\Util;

use function count;
use function array_key_last;
use function preg_replace;

/**
 * Provides common InnerTube parsing utilities for Rehike.
 * 
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
            else if (isset($source->content))
            {
                return self::getText(self::commandRunsToRuns($source));
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
        $url = @$source->navigationEndpoint->commandMetadata->webCommandMetadata->url
            ?? @$source->navigationEndpoint->confirmDialogEndpoint->content->confirmDialogRenderer->confirmButton->buttonRenderer->command->urlEndpoint->url
            ?? @$source->commandMetadata->webCommandMetadata->url
            ?? null;
            
        if ($url)
        {
            if (str_starts_with($url, "/@"))
            {
                return self::fixNewUsernameUrl($url, $source);
            }
            
            return $url;
        }
        
        return null;
    }
    
    /**
     * PATCH (izzy): YouTube fucked up parsing new username URLs.
     * 
     * If a channel has a modern username which is the same as a legacy one, then
     * the legacy one is preferred by YouTube's server-side parser, and you will
     * visit the legacy channel.
     * 
     * For example:
     * 
     * /@Stryder7x (UCYDnJiF0_RqSjkjvjRbG1tA) -> /user/Stryder7x (UC7O-LSXY_CWSI3AIoIsamqg)
     * 
     * Because this can be quite confusing, we'll just fix YouTube's fuck up in Rehike
     * by always using /channel/ links for /@ URLs.
     */
    public static function fixNewUsernameUrl(string $url, object $source): ?string
    {
        $channelId = @$source->navigationEndpoint->browseEndpoint->browseId
            ?? @$source->browseEndpoint->browseId
            ?? null;
        $urlParts = explode("/", trim($url, "/"));
        $subPage = implode("/", array_slice($urlParts, 1));
        
        return empty($subPage)
            ? "/channel/$channelId"
            : "/channel/$channelId/$subPage";
    }

    /**
     * Get the thumbnail of an InnerTube API response field.
     * 
     * This will work on anything from video renderers to avatar images
     * for users.
     */
    public static function getThumb(object $container, int $height = 0, bool $correctForShorts = false): ?string
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
        //
        // Also skip station renderers. If we don't, they will be incorrectly
        // classified as Shorts, and be stripped of their signature. This isn't
        // a problem for other thumbnails, but it will break station thumbnails
        // entirely.
        if (
            $correctForShorts &&
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
            $isShort = !($ratio >= 1.7 && $ratio < 1.8) || @$container->isOriginalAspectRatio;

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
    
    /**
     * Helper function for finding thumbnail overlays.
     * 
     * This iterates the overlays array and searches for the
     * provided identifier.
     * 
     * This is meant to be used as a helper function for Twig.
     * 
     * @param object $array of the thumbnail overlays
     * @param string $name of the overlay identifier
     * 
     * @return ?object
     */
    public static function getThumbnailOverlay(object $array, string $name): ?object
    {
        if (!isset($array->thumbnailOverlays)) return null;

        // Iterate the array and figure out the thumbnail overlay
        foreach ($array->thumbnailOverlays as $index => $contents)
        {
            // InnerTube API formats thumbnail overlays as
            // keys within an object. Fortunately, this is pretty
            // easy to check within PHP.
            if (isset($contents->$name)) return $contents->$name;
        }
        
        // Return null if the index doesn't exist.
        return null;
    }

    /**
     * Convert a commandRuns object to the conventional runs format.
     * Does not support text formatting, only links.
     * Was originally only for description on watch page.
     * 
     * Original comment:
     * Welp, the fucktards have done it again. The shitty description experiment is back,
     * and it's just as painful as it was before. Do they even have any fucking idea what
     * they're doing? The link color is fucking hardcoded blue for god's sake. There is
     * no fucking way they can be serious about this. They have to be fucking with us at 
     * this point. What a bunch of fucking retards.
     *     - Love, Aubrey <3
     * 
     * Anyways, this function just converts it back to the standard runs format.
     * 
     * We use mb_substr with UTF-8 here, because the indices are set up for a JS environment.
     * 
     * @param object $cruns     Object containing commandRuns
     * 
     * @return ?object
     */
    public static function commandRunsToRuns(object $cruns): ?object
    {
        // If there's no links
        if (!isset($cruns->commandRuns))
        {
            return (object) [
                "runs" => [
                    (object) [
                        "text" => $cruns->content
                    ]
                ]
            ];
        }

        $runs = [];

        // Start at the beginning of the string
        $start = 0;

        foreach ($cruns->commandRuns as $run)
        {
            // Text from before the link
            $beforeText = self::mb_substr_ex($cruns->content, $start, $run->startIndex - $start);

            if (!empty($beforeText))
            {
                $runs[] = (object) [
                    "text" => $beforeText
                ];
            }

            // Add the actual link
            $text = self::mb_substr_ex($cruns->content, $run->startIndex, $run->length);
            $endpoint = $run->onTap->innertubeCommand;
            $runs[] = (object) [
                "text" => $text,
                "navigationEndpoint" => $endpoint
            ];

            $start = $run->startIndex + $run->length;
        }

        // Add the text after the last link
        $lastText = self::mb_substr_ex($cruns->content, $start, null);
        if (!empty($lastText))
        {
            $runs[] = (object) [
                "text" => $lastText
            ];
        }

        return (object) [
            "runs" => $runs
        ];
    }

    /**
     * Custom mb_substr function for commandRunsToRuns.
     * The default mb_substr will cause breakage with emojis.
     * 
     * @see   commandRunsToRuns()
     * 
     * @param string $str     String to crop.
     * @param int    $offset  Zero-indexed offset to begin cropping at.
     * @param ?int   $length  Length of the cropped string.
     * 
     * @return string
     */
    private static function mb_substr_ex(string $str, int $offset, ?int $length): string {
        $bmp = [];
        for($i = 0; $i < mb_strlen($str); $i++)
        {
            $mb_substr = mb_substr($str, $i, 1);
            $mb_ord = mb_ord($mb_substr);
            $bmp[] = $mb_substr;
            if ($mb_ord > 0xFFFF)
            {
                $bmp[] = "";
            }
        }
        return implode("", array_slice($bmp, $offset, $length));
    }
}