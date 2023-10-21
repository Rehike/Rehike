<?php
namespace Rehike\Model\Rehike\Config;
use Rehike\i18n\i18n;
use Rehike\Model\Traits\NavigationEndpoint;
use Rehike\RehikeConfigManager as ConfigManager;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MAlert;

class ConfigModel {
    public static function bake($tab, $status = null) {
        $response = (object) [];
        $i18n = i18n::getNamespace("rehike/config");
        $tabs = (object) $i18n->getAllTemplates()->tabs;
        $props = json_decode(json_encode($i18n->getAllTemplates()->props))->{$tab};

        $response->tab = $tab;

        $response->sidebar = (object) [
            "creatorSidebarRenderer" => (object) [
                "sections" => [
                    (object) [
                        "creatorSidebarSectionRenderer" => (object) [
                            "title" => (object) [
                                "simpleText" => $i18n->get("title")
                            ],
                            "targetId" => "rehike-config",
                            "isSelected" => true,
                            "items" => []
                        ]
                    ]
                ],
                "footButtons" => [
                    (object)[
                        "buttonRenderer" => self::getDisableRehikeButton()
                    ]
                ]
            ]
        ];

        if ($status != null) {
            $response->alerts = [];
            switch ($status) {
                case "success":
                    $response->alerts[] = new MAlert([
                        "type" => MAlert::TypeSuccess,
                        "text" => $i18n->get("saveChangesSuccess")
                    ]);
                    break;
                case "failure":
                    $response->alerts[] = new MAlert([
                        "type" => MAlert::TypeError,
                        "text" => $i18n->get("saveChangesFailure")
                    ]);
                    break;
            }
        }

        foreach ($tabs as $name => $text) {
            $response->sidebar->creatorSidebarRenderer->sections[0]
            ->creatorSidebarSectionRenderer->items[] = 
            self::buildCreatorSidebarItem(
                $text,
                "/rehike/config/{$name}",
                ($name == $tab)
            );
        }

        $response->content = (object) [
            "title" => $tabs->{$tab},
            "contents" => []
        ];

        $contents = &$response->content->contents;
        foreach (ConfigManager::getConfig()->{$tab} as $option => $value) {
            switch (ConfigManager::getConfigType("{$tab}.{$option}")) {
                case "bool":
                    $contents[] = (object) [
                        "checkboxRenderer" => (object) [
                            "title" => $props->{$option}->title ?? null,
                            "subtitle" => $props->{$option}->subtitle ?? null,
                            "checked" => $value ? true : false,
                            "name" => "$tab.$option",
                        ]
                    ];
                    break;
                case "enum":
                    $values = [];
                    $selectedValue = null;

                    foreach ($props->{$option} ->values as $name => $text) {
                        $values[] = (object) [
                            "text" => $text,
                            "value" => $name,
                            "selected" => ($value == $name)
                        ];

                        if ($value == $name) $selectedValue = $value;
                    }

                    $contents[] = (object) [
                        "selectRenderer" => (object) [
                            "label" => $props->{$option}->title,
                            "name" => "$tab.$option",
                            "values" => $values,
                            "selectedValue" => $selectedValue
                        ]
                    ];
                    break;
            }
        }

        $response->content->saveButton = new MButton([
            "style" => "STYLE_PRIMARY",
            "text" => (object) [
                "simpleText" => $i18n->get("saveChanges")
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

    private static function getDisableRehikeButton(): MButton
    {
        $i18n = i18n::getNamespace("rehike/disable_rehike");
        $isDisabled = ConfigManager::getConfigProp("hidden.disableRehike");

        $buttonText = $isDisabled
            ? $i18n->get("rhSettingsEnableRehike")
            : $i18n->get("disableRehike");

        return new MButton([
            "style" => "STYLE_DARK",
            "class" => [ "rehike-config-disable-rehike-button" ],
            "attributes" => [
                "disable-rehike-action" => $isDisabled ? "enable" : "disable",
                "dialog-header-text" => $i18n->get("disableRehikeInfoHeader"),
                "dialog-header-description" => $i18n->get("disableRehikeInfoDescription"),
                "dialog-header-button-cancel" => $i18n->get("disableRehikeInfoCancel"),
                "dialog-header-button-disable" => $i18n->get("disableRehikeInfoDisable")
            ],
            "text" => (object)[
                "simpleText" => $buttonText
            ]
        ]);
    }
}