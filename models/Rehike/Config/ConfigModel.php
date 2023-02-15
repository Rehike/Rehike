<?php
namespace Rehike\Model\Rehike\Config;
use Rehike\i18n;
use Rehike\Model\Traits\NavigationEndpoint;
use Rehike\RehikeConfigManager as ConfigManager;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MAlert;

class ConfigModel {
    public static function bake($tab, $status = null) {
        $response = (object) [];
        $i18n = i18n::newNamespace("rehike/config") -> registerFromFolder("i18n/rehike/config");
        $tabs = (object) $i18n -> tabs;
        $props = json_decode(json_encode($i18n -> props)) -> {$tab};

        $response -> tab = $tab;

        $response -> sidebar = (object) [
            "creatorSidebarRenderer" => (object) [
                "sections" => [
                    (object) [
                        "creatorSidebarSectionRenderer" => (object) [
                            "title" => (object) [
                                "simpleText" => $i18n -> title
                            ],
                            "targetId" => "rehike-config",
                            "isSelected" => true,
                            "items" => []
                        ]
                    ]
                ]
            ]
        ];

        if ($status != null) {
            $response -> alerts = [];
            switch ($status) {
                case "success":
                    $response -> alerts[] = new MAlert([
                        "type" => MAlert::TypeSuccess,
                        "text" => $i18n -> saveChangesSuccess
                    ]);
                    break;
                case "failure":
                    $response -> alerts[] = new MAlert([
                        "type" => MAlert::TypeError,
                        "text" => $i18n -> saveChangesFailure
                    ]);
                    break;
            }
        }

        foreach ($tabs as $name => $text) {
            $response -> sidebar -> creatorSidebarRenderer -> sections[0]
            -> creatorSidebarSectionRenderer -> items[] = 
            self::buildCreatorSidebarItem(
                $text,
                "/rehike/config/{$name}",
                ($name == $tab)
            );
        }

        $response -> content = (object) [
            "title" => $tabs -> {$tab},
            "contents" => []
        ];

        $contents = &$response -> content -> contents;
        foreach (ConfigManager::getConfig() -> {$tab} as $option => $value) {
            switch (ConfigManager::getConfigType("{$tab}.{$option}")) {
                case "bool":
                    $contents[] = (object) [
                        "checkboxRenderer" => (object) [
                            "title" => $props -> {$option} -> title ?? null,
                            "subtitle" => $props -> {$option} -> subtitle ?? null,
                            "checked" => $value ? true : false,
                            "name" => "$tab.$option",
                        ]
                    ];
                    break;
                case "enum":
                    $values = [];
                    $selectedValue = null;

                    foreach ($props -> {$option} -> values as $name => $text) {
                        $values[] = (object) [
                            "text" => $text,
                            "value" => $name,
                            "selected" => ($value == $name)
                        ];

                        if ($value == $name) $selectedValue = $value;
                    }

                    $contents[] = (object) [
                        "selectRenderer" => (object) [
                            "label" => $props -> {$option} -> title,
                            "name" => "$tab.$option",
                            "values" => $values,
                            "selectedValue" => $selectedValue
                        ]
                    ];
                    break;
            }
        }

        $response -> content -> saveButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "text" => (object) [
                "simpleText" => $i18n -> saveChanges
            ],
            "type" => "submit",
            "class" => ["rehike-config-save-button"],
            "isDisabled" => true
        ]);

        return $response;
    }

    public static function buildCreatorSidebarItem($title, $href, $selected = false) {
        return (object) [
            "creatorSidebarItemRenderer" => (object) [
                "title" => (object) [
                    "simpleText" => $title
                ] ,
                "navigationEndpoint" => NavigationEndpoint::createEndpoint($href),
                "isSelected" => $selected
            ]
        ];
    }
}