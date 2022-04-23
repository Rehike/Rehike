<?php

namespace Rehike;

use Rehike\ConfigManager\ConfigManager;

class RehikeConfigManager extends ConfigManager
{
    protected static $defaultConfig =
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

        return parent::loadConfig();
    }
}