<?php
namespace Rehike;

use Rehike\ConfigManager\Config;
use YukisCoffee\PropertyAtPath;

use Rehike\ConfigManager\Properties\{
    BoolProp,
    EnumProp,
    PropGroup,
    DependentProp,
    StringProp
};

/**
 * Defines Rehike configuration definitions.
 * 
 * @author Taniko Yamamoto <kiraicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ConfigDefinitions
{
    public static function getConfigDefinitions(): array
    {
        return [
            "appearance" => [
                "modernLogo" => new BoolProp(true),
                "uploadButtonType" => new EnumProp("MENU", [
                    "BUTTON",
                    "ICON",
                    "MENU"
                ]),
                "largeSearchResults" => new BoolProp(true),
                "swapSearchViewsAndDate" => new BoolProp(false),
                "showVersionInFooter" => new BoolProp(true),
                "usernamePrepends" => new BoolProp(false),
                "useRyd" => new BoolProp(true),
                "noViewsText" => new BoolProp(false),
                "movingThumbnails" => new BoolProp(true),
                "cssFixes" => new BoolProp(true),
                "watchSidebarDates" => new BoolProp(false),
                "watchSidebarVerification" => new BoolProp(false),
                "oldBestOfYouTubeIcons" => new BoolProp(false),
                "smallPlayer" => new BoolProp(true),
                "enableAdblock" => new BoolProp(true)
            ],
            "experiments" => [
                "useSignInV2" => new BoolProp(false),
                "disableSignInOnHome" => new BoolProp(false)
            ],
            "advanced" => [
                "enableDebugger" => new BoolProp(false)
            ],
            "hidden" => [
                "language" => new StringProp("en-US"),
                "securityIgnoreWindowsServerRunningAsSystem" =>
                    new BoolProp(false),
                "disableRehike" => new BoolProp(false),
                "enableProfiler" => new BoolProp(false)
            ]
        ];
    }
}