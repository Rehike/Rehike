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
                new PropGroup(...[
                    // Temporarily a dependent property until the experimental
                    // phase is over.
                    "playerChoice" => new DependentProp(
                        "experiments.displayPlayerChoice",
                        new EnumProp("CURRENT", [
                            "CURRENT",
                            "PLAYER_2014",
							"PLAYER_2015",
							"PLAYER_2015_NEW",
                            "PLAYER_2020",
                            "PLAYER_2022"
                        ])
                    ),
					"classicPlayerColor" =>
                        new DependentProp(
                            "appearance.playerChoice == PLAYER_2014 || appearance.playerChoice == PLAYER_2015 || appearance.playerChoice == PLAYER_2015_NEW", // temporarily 2014 player only as idk how to set it on newer players
                            new EnumProp("RED", [
								"RED",
								"WHITE"
							])
                        ),
					"classicPlayerTheme" =>
                        new DependentProp(
                            "appearance.playerChoice == PLAYER_2014 || appearance.playerChoice == PLAYER_2015", // deprecated when they introduced the current player design
                            new EnumProp("DARK", [
								"DARK",
								"LIGHT"
							])
                        ),
					"smallPlayer" => new DependentProp(
                            "appearance.playerChoice == PLAYER_2022 || appearance.playerChoice == CURRENT",
							new BoolProp(true)
						),
                    "classicPlayerForcePersistentControls" =>
                        new DependentProp(
                            "appearance.playerChoice == PLAYER_2014 || appearance.playerChoice == PLAYER_2015",
                            new BoolProp(true)
                        ),
                ]),
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
                "enableAdblock" => new BoolProp(true)
            ],
            "experiments" => [
                "displayPlayerChoice" => new BoolProp(false),
                "useSignInV2" => new BoolProp(false),
                "disableSignInOnHome" => new BoolProp(false),
                "encryptedStreamsDO_NOT_USE_UNLESS_YOU_KNOW_WHAT_YOU_ARE_DOING" => new BoolProp(true)
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