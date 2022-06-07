<?php

namespace Rehike;

use Rehike\ConfigManager\ConfigManager;

class RehikeConfigManager extends ConfigManager
{
    public static $defaultConfig =
        [
            "useRingoBranding" => true,
            "enableCreationMenu" => true,
            "useOldRoboto" => false,
            "showUploadDateOnWatchRecommended" => false,
            "useWebV2HomeEndpoint" => false,
            "useOldUploadButton" => false
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