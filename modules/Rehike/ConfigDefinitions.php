<?php
namespace Rehike;

use Rehike\ConfigManager\Config;
use Rehike\PropertyAtPath;

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
 * @author Isabella Lulamoon <kawapure@gmail.com>
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
                "branding" => new EnumProp("BRANDING_2024_RINGO2", [
                    "BRANDING_2024_RINGO2",
                    "BRANDING_2017_RINGO",
                    "BRANDING_2015"
                ]),
                "uploadButtonType" => new EnumProp("MENU", [
                    "BUTTON",
                    "ICON",
                    "MENU"
                ]),
                "showNewInfoOnChannelAboutPage" => new BoolProp(true),
                "largeSearchResults" => new BoolProp(true),
                "swapSearchViewsAndDate" => new BoolProp(false),
                "showOldUploadedOnText" => new BoolProp(false),
                "useLegacyRoboto" => new BoolProp(false),
                "showVersionInFooter" => new BoolProp(true),
                "usernamePrepends" => new BoolProp(false),
                "useRyd" => new BoolProp(true),
                "enableSponsorblockFixes" => new BoolProp(true),
                "noViewsText" => new BoolProp(false),
                "movingThumbnails" => new BoolProp(true),
                "cssFixes" => new BoolProp(true),
                "watchSidebarDates" => new BoolProp(false),
                "watchSidebarVerification" => new BoolProp(false),
                "oldBestOfYouTubeIcons" => new BoolProp(false),
                "enableAdblock" => new BoolProp(true),
            ],
            "experiments" => [
                "displayPlayerChoice" => (new BoolProp(false))->registerUpdateCb(function() {
                    // When this configuration option is changed, there is an expectation from
                    // the user for it to reset the player setting back to the latest player,
                    // as would be the only possible state prior to enabling the option.
                    // https://github.com/Rehike/Rehike/issues/593#issuecomment-2272158302
                    
                    Config::setConfigProp("appearance.playerChoice", "CURRENT");
                    Config::dumpConfig();
                }),
                "useSignInV2" => new BoolProp(false),
                "asyncAttestationRequest" => new BoolProp(true),
                "disableSignInOnHome" => new BoolProp(false),
                "tickInjectionForScheduling" => (new BoolProp(false))->registerUpdateCb(function() {
                    // When this configuration property changes, the contents of the PHP files
                    // change virtually without being touched on disk, so we just manually
                    // clear the opcache to recompile the scripts:
                    if (function_exists("opcache_reset"))
                        opcache_reset();
                }),
                "temp20240827_playerMode" => new EnumProp("USE_WEB_V2", [
                    "USE_WEB_V2",
                    "USE_EMBEDDED_PLAYER_REQUEST",
                    "USE_EMBEDDED_PLAYER_DIRECTLY",
                ]),
            ],
            "advanced" => [
                "dnsAddress" => new StringProp("1.1.1.1"),
                "disableSslVerification" => new BoolProp(false),
                "enableDebugger" => new BoolProp(false),
                "developer" => [
                    "ignoreUnresolvedPromises" => new BoolProp(false)
                ]
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
    
    public static function migrateOldOptions(): void
    {
        $changedAnything = false;
        
        $migrateAndRemoveOriginal = function(string $prop, \Closure $cb) use (&$changedAnything) {
            $originalProperty = null;
            $originalProperty = Config::getConfigProp($prop);
            if ($originalProperty !== null)
            {
                $cb($originalProperty);
                Config::removeConfigProp($prop);
                $changedAnything = true;
            }
        };
        
        $migrateAndRemoveOriginal("appearance.modernLogo", fn($modernLogo) =>
            Config::setConfigProp("appearance.branding", $modernLogo
                ? "BRANDING_2024_RINGO2"
                : "BRANDING_2015"
            )
        );
        
        if ($changedAnything)
        {
            Config::dumpConfig();
        }
    }
}
