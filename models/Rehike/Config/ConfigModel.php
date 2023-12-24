<?php
namespace Rehike\Model\Rehike\Config;

use AllowDynamicProperties;
use Rehike\Model\Rehike\Panel\RehikePanelPage;

use Rehike\i18n\i18n;
use Rehike\Model\Traits\NavigationEndpoint;
use Rehike\ConfigManager\Config;
use Rehike\Model\Common\MButton;
use Rehike\Model\Common\MAlert;

class ConfigModel extends RehikePanelPage
{
    /**
     * The ID of the selected tab.
     */
    public string $tab;

    public array $alerts = [];

    public object $content;

    public function __construct(string $tabId)
    {
        $this->content = (object)[];
        parent::__construct($tabId);
    }

    public static function bake(string $tab, $status = null) {
        
        $response = new self($tab);
        $i18n = i18n::getNamespace("rehike/config");
        $tabs = (object) $i18n->getAllTemplates()->tabs;
        $props = json_decode(json_encode($i18n->getAllTemplates()->props))->{$tab};

        $response->tab = $tab;

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

        $response->content = (object) [
            "title" => $tabs->{$tab},
            "contents" => []
        ];

        $contents = &$response->content->contents;
        foreach (Config::getConfig()->{$tab} as $option => $value) {
            switch (Config::getConfigType("{$tab}.{$option}")) {
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
}