<?php
namespace Rehike\Misc;

use Rehike\RehikeConfigManager;
use Rehike\Version\VersionController;

use Rehike\Util\ParsingUtils;

use stdClass;

/**
 * Defines the `rehike` variable exposed to Twig-land.
 * 
 * This implements unique properties directly on the class. The child
 * class will implement all alias properties.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
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
     * Initialise all utilities.
     */
    public function __construct()
    {
        $this->config = RehikeConfigManager::loadConfig();
        $this->version = (object)VersionController::$versionInfo;
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
    public static function getThumb(?object $obj, int $height = 0): string
    {
        if (null == $obj) return "//i.ytimg.com";
        
        return ParsingUtils::getThumb($obj, $height) ?? "//i.ytimg.com/";
    }

    /**
     * Alias for ParsingUtils::getThumbnailOverlay() for templating use.
     */
    public static function getThumbnailOverlay(object $array, string $name): ?object
    {
        return ParsingUtils::getThumbnailOverlay($array, $name);
    }
}