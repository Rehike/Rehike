<?php

namespace Rehike;

use Rehike\ConfigManager\ConfigManager;
use YukisCoffee\PropertyAtPath;

/**
 * Implements the Rehike-specific portions of the
 * config manager system.
 * 
 * @author Taniko Yamamoto <kiraicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class RehikeConfigManager extends ConfigManager
{
    public static $defaultConfig =
    [
        "appearance" => [
            "modernLogo" => true,
            "uploadButtonType" => "MENU",
            "largeSearchResults" => true,
            "showVersionInFooter" => true,
            "usernamePrepends" => false,
            "useRyd" => true,
            "noViewsText" => false,
            "movingThumbnails" => true,
            "cssFixes" => true,
            "watchSidebarDates" => false,
            "teaserReplies" => false,
            "oldBestOfYouTubeIcons" => false
        ],
        "advanced" => [
            "enableDebugger" => false
        ]
    ];

    public static $types =
    [
        "appearance" => [
            "modernLogo" => "bool",
            "uploadButtonType" => "enum",
            "largeSearchResults" => "bool",
            "showVersionInFooter" => "bool",
            "usernamePrepends" => "bool",
            "useRyd" => "bool",
            "noViewsText" => "bool",
            "movingThumbnails" => "bool",
            "cssFixes" => "bool",
            "watchSidebarDates" => "bool",
            "teaserReplies" => "bool",
            "oldBestOfYouTubeIcons" => "bool"
        ],
        "advanced" => [
            "enableDebugger" => "bool"
        ]
    ];

    // Old config compatability map
    // These are PropertyAtPath (JS-style) paths
    public static $compatabilityMap = [
        "useRingoBranding" => "appearance.modernLogo",
        "uploadMenuType" => "appearance.uploadButtonType",
        "versionInFooter" => "appearance.showVersionInFooter",
        "useReturnYouTubeDislike" => "appearance.useRyd",
        "enableRehikeDebugger" => "advanced.enableDebugger",
        "largeSearchThumbs" => "appearance.largeSearchResults",
        "byTextOnByline" => "appearance.usernamePrepends",
        "noViewsText" => "appearance.noViewsText",
        "movingThumbnails" => "appearance.movingThumbnails",
        "hhCSSFixes" => "appearance.cssFixes",
        "watchSidebarDates" => "appearance.watchSidebarDates",
        "teaserReplies" => "appearance.teaserReplies",
        "oldBestOfYouTubeIcons" => "appearance.oldBestOfYouTubeIcons",
        "guideOnWatchPage" => "REMOVE",
        "useWebV2HomeEndpoint" => "REMOVE",
        "useGridHomeStyle" => "REMOVE",
        "accountPickerYtStudio" => "REMOVE"
    ];
    
    /**
     * If configuration doesn't exist upon
     * attempt to load it, save it
     * 
     * @return object
     */
    public static function loadConfig()
    {
        if (!file_exists( self::$file ))
        {
            static::dumpDefaultConfig();
        }

        parent::loadConfig();

        $redump = false;
        
        // Make sure new defaults get added to the config file.
        // json_encode wrapped in json_decode as an quick 'n easy
        // way to cast all associative arrays to objects
        foreach (json_decode(json_encode(self::$defaultConfig)) as $key => $value)
        {
            if (!isset(self::$config->{$key}))
            {
                self::$config->{$key} = $value;
                
                $redump = true;
            }
        }

        foreach (self::$compatabilityMap as $key => $value) {
            try {
                if ($value == "REMOVE") {
                    PropertyAtPath::unset(self::$config, $key);
                } else {
                    $oldCfg = PropertyAtPath::get(self::$config, $key);
                    if ($oldCfg !== null) {
                        PropertyAtPath::set(self::$config, $value, $oldCfg);
                        PropertyAtPath::unset(self::$config, $key);
                    }
                }

                $redump = true;
            } catch (\YukisCoffee\PropertyAtPathException $e) {}
        }


        if ($redump) self::dumpConfig();

        return self::$config;
    }
}