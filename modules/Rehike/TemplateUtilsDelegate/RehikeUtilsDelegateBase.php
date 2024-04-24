<?php
namespace Rehike\TemplateUtilsDelegate;

use Rehike\ConfigManager\Config;
use Rehike\Version\VersionController;
use Rehike\i18n\i18n;

use Rehike\Util\ParsingUtils;
use Rehike\Util\Base64Url;

use Com\Youtube\Innertube\Navigation\NavigationEndpoint;
use Com\Youtube\Innertube\Navigation\NavigationEndpoint\BrowseEndpoint;
use Com\Youtube\Innertube\Navigation\NavigationEndpoint\UrlEndpoint;

use stdClass;

/**
 * Defines the `rehike` variable exposed to Twig-land.
 * 
 * This implements unique properties directly on the class. The child
 * class will implement all alias properties.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author Daylin Cooper <dcoop2004@gmail.com>
 * @author The Rehike Maintainers
 */
abstract class RehikeUtilsDelegateBase extends stdClass
{
    /**
     * Stores the current Rehike configuration.
     */
    public object $config;

    /**
     * Provides version information about the current Rehike setup.
     */
    public object $version;

    /**
     * Valid lockup types for getLockupInfo.
     * 
     * @var string[]
     */
    public const VALID_LOCKUP_TYPES = [
        "video",
        "channel",
        "playlist",
        "radio",
        "movie",
        "show",
        "station" // Album
    ];

    /**
     * Valid meta types in order for getMeta.
     * 
     * @var string[]
     */
    public const VALID_METAS = [
        "viewCountText",
        "publishedTimeText",
        "videoCountText"
    ];

    /**
     * Initialise all utilities.
     */
    public function __construct()
    {
        $this->config = Config::loadConfig();
        $this->version = VersionController::$versionInfo;
        $this->version->semanticVersion = VersionController::getVersion();
    }

    /**
     * Alias for ParsingUtils::getText() for templating use.
     */
    public static function getText(mixed $source): string
    {
        return ParsingUtils::getText($source) ?? "";
    }

    /**
     * Alias for ParsingUtils::getUrl() for templating use.
     */
    public static function getUrl(mixed $source): string
    {
        return ParsingUtils::getUrl($source) ?? "";
    }

    /**
     * Alias for ParsingUtils::getThumb() for templating use.
     */
    public static function getThumb(?object $obj, int $height = 0, bool $correctForShorts = false): string
    {
        if (null == $obj) return "//i.ytimg.com";
        
        return ParsingUtils::getThumb($obj, $height, $correctForShorts) ?? "//i.ytimg.com/";
    }

    /**
     * Alias for ParsingUtils::getThumbnailOverlay() for templating use.
     */
    public static function getThumbnailOverlay(object $array, string $name): ?object
    {
        return ParsingUtils::getThumbnailOverlay($array, $name);
    }

    /**
     * Convert an object to an associative array.
     * 
     * This is needed in order to iterate the keys of an object
     * in Twig. Twig only supports iterating associative arrays, not
     * objects.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     * @author The Rehike Maintainers
     * 
     * @param string $obj to cast
     * @return array of the casted object
     */
    public static function obj2arr($obj): array
    {
        return (array)$obj;
    }

    /**
     * Generate a template level RID.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     */
    public static function generateRid(): int
    {
        return rand(100000, 999999);
    }

    /**
     * Extract the byline text from an InnerTube renderer model.
     */
    public static function getByline(?object $renderer): ?object
    {
        if (isset($renderer->longBylineText))
        {
            return $renderer->longBylineText;
        }
        else if (isset($renderer->shortBylineText))
        {
            return $renderer->shortBylineText;
        }
            
        return null;
    }

    /**
     * Get a description snippet from the InnerTube renderer.
     */
    public static function getDescSnippet(?object $renderer)
    {
        if (isset($renderer->descriptionSnippet))
        {
            return $renderer->descriptionSnippet;
        }
        else if (isset($renderer->detailedMetadataSnippets[0]->snippetText))
        {
            return $renderer->detailedMetadataSnippets[0]->snippetText;
        }

        return null;
    }

    /**
     * Get an InnerTube content renderer ("lockup")'s info.
     */
    public static function getLockupInfo(?object $renderer): ?object
    {
        $response = (object) [];
    
        // Get the name of the renderer
        foreach($renderer as $key => $val) $rendName = $key;
        $response->info = $renderer->$rendName;
        $response->style = (strpos($rendName, "grid") > -1) ? "grid" : "tile";
        
        // Extract the type name from the InnerTube object name.
        $response->type = strtolower(
            str_replace("compact", "", str_replace("grid", "", str_replace("Renderer", "", $rendName)))
        );
    
        if ($a = @$response->info->thumbnails[0])
        {
            $response->thumbArray = $a;
        }
        else if ($a = @$response->info->thumbnailRenderer->showCustomThumbnailRenderer->thumbnail)
        {
            $response->thumbArray = $a;
        }
        else
        {
            $response->thumbArray = $response->info->thumbnail ?? null;
        }

        if (in_array($response->type, self::VALID_LOCKUP_TYPES))
        {
            return $response;
        }

        return null;
    }

    /**
     * Get the metadata from a lockup renderer.
     */
    public static function getMeta(?object $renderer): ?array
    {
        $validMetas = self::VALID_METAS;
        $metas = [];

        /* Swap date and views */
        if (@$renderer->dateBeforeViews)
        {
            [$validMetas[0], $validMetas[1]] = [$validMetas[1], $validMetas[0]];
        }
    
        foreach ($validMetas as $meta)
        {
            if (
                isset($renderer->{$meta}) && (
                    isset($renderer->{$meta}->simpleText) ||
                    isset($renderer->{$meta}->runs)
                )
            )
            {
                $metas[] = $renderer->{$meta};
            }
        }
    
        return count($metas) > 0 ? $metas : null;
    }

    /**
     * Gets the length of a video as a formatted time string, i.e. 1:30 for a
     * video that is one minute and thirty seconds long.
     */
    public static function getVideoTime(?object $obj): ?string
    {
        $regexes = i18n::getNamespace("regex");

        if (isset($obj->lengthText))
        {
            return $obj->lengthText->simpleText;
        }
        else if (isset($obj->thumbnailOverlays))
        {
            for ($i = 0; $i < count($obj->thumbnailOverlays); $i++)
            {
                if (isset($obj->thumbnailOverlays[$i]->thumbnailOverlayTimeStatusRenderer))
                {
                    $lengthText = $obj->thumbnailOverlays[$i]->thumbnailOverlayTimeStatusRenderer
                        ->text->simpleText;
                }
            }
    
            if (!isset($lengthText))
            {
                return null;
            }
            else
            {
                if ($lengthText == "SHORTS")
                {
                    // only match seconds, if the video has the shorts timestamp we can assume two things
                    // - the video is at MOST 1 minute
                    // - if it has no seconds in the accessibility label it is 100% exactly 1 minute long
                    preg_match(
                        $regexes->get("videoTimeIsolator"), 
                        $obj->title->accessibility->accessibilityData->label, $matches
                    );

                    if (!isset($matches[0]))
                    {
                        return "1:00";
                    }
                    else
                    {
                        $lengthText = preg_replace($regexes->get("secondsIsolator"), "", $matches[0]);
                        return "0:" . str_pad($lengthText, 2, "0", STR_PAD_LEFT);
                    }
                }
                else if ($lengthText == "LIVE")
                { 
                    // some endpoints have LIVE timestamp instead of badge
                    return null;
                }
                else
                {
                    return $lengthText;
                }
            }
        }
    
        return null;
    }

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
    public static function getWatchedPercent($array)
    {
        if (!isset($array->thumbnailOverlays )) return null;
    
        foreach ($array->thumbnailOverlays as $index => $contents)
        {
            if (isset($contents->thumbnailOverlayResumePlaybackRenderer))
            {
                return $contents->thumbnailOverlayResumePlaybackRenderer->percentDurationWatched;
            }
        }
        
        // Return null if the index doesn't exist.
        return null;
    }

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
    public static function getWLOverlay($array)
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
    }

    /**
     * Resolve a guide endpoint (used for some attributes on guide items, like the IDs).
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     * @author The Rehike Maintainers
     * 
     * @param object $guideItem
     * @return string
     */
    public static function resolveGuideEndpoint(?object $guideItem): string
    {
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
    }

    /**
     * Converts a size constant into its CSS class name format.
     */
    public static function resolveSize(string $const): string
    {
        return strtolower(str_replace(["SIZE_", "_"], ["", "-"], $const));
    }

    /**
     * Converts a style name constant into its CSS class name format.
     */
    public static function resolveStyle(string $const): string
    {
        $styleOverrides = (object) [
            "STYLE_BLUE_TEXT" => "STYLE_PRIMARY"
        ];
    
        if (isset($styleOverrides->{$const}))
        {
            $const = $styleOverrides->{$const};
        }
    
        return strtolower(str_replace(["STYLE_", "_"], ["", "-"], $const));
    }

    /**
     * Serialise a guide navigation endpoint in URL-base64 protobuf.
     * 
     * This is very accurate to the official Hitchhiker implementation.
     * 
     * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
     * @author The Rehike Maintainers
     * 
     * @param object $endpoint
     * @return string
     */
    public static function serializeEndpoint(?object $endpoint): string
    {
        $pb = new NavigationEndpoint();
    
        if (isset($endpoint->browseEndpoint))
        {
            $pb->setBrowseEndpoint(new BrowseEndpoint([
                "browse_id" => $endpoint->browseEndpoint->browseId
            ]));
        }
        else if (isset($endpoint->urlEndpoint))
        {
            $pb->setUrlEndpoint(new UrlEndpoint([
                "url" => $endpoint->urlEndpoint->url
            ]));
        }
    
        $data = $pb->serializeToString();
    
        return Base64Url::encode($data);
    }
}