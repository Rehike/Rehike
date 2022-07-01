<?php

namespace Rehike;

use Rehike\ConfigManager\ConfigManager;

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
            "useRingoBranding" => true,
            "uploadMenuType" => "MENU",
            "dateOnWatchSidebarItems" => false,
            "useWebV2HomeEndpoint" => false,
            "versionInFooter" => true,
            "useReturnYouTubeDislike" => true,
            "enableRehikeDebugger" => false,
            "largeSearchThumbs" => true,
            "smallWatchSidebarItems" => false,
            "oldLockupStyle" => false,
            "largeWatchTitle" => false,
            "oldShareIcon" => false,
            "byTextOnByline" => true,
            "oldGuideButton" => false
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
        foreach (self::$defaultConfig as $key => $value)
        {
            if (!isset(self::$config->{$key}))
            {
                self::$config->{$key} = $value;
                
                $redump = true;
            }
        }

        if ($redump) self::dumpConfig();

        return self::$config;
    }
}