<?php
namespace Rehike\i18n\Internal;

use Rehike\i18n\i18n;
use Rehike\FileSystem;
use Rehike\ConfigManager\Config;

/**
 * Implements core services pertaining to the Rehike i18n system.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class Core
{
    public static function getInnertubeLanguageId(): string
    {
        $config = i18n::getConfigApi();

        $root = $config->getRootDirectory();
        
        $languageIds = $config->getPreferredLanguageIds();
        $languageIds[] = $config->getDefaultLanguageId();

        foreach ($languageIds as $id)
        {
            if (FileSystem::fileExists("$root/$id/_rhcfg.json"))
            {
                $file = FileSystem::getFileContents("$root/$id/_rhcfg.json");
                $data = json_decode($file);
                return $data->innertubeLanguageId ?? "en-US";
            }
        }

        // Fallback:
        return "en-US";
    }

    public static function getInnertubeGeolocation(): string
    {
        $configGl = Config::getConfigProp("hidden.gl");

        return $configGl ?? "US";
    }
}