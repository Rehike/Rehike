<?php
namespace Rehike\Model\Rehike\Panel;

use Rehike\Model\Traits\NavigationEndpoint;
use Rehike\i18n\i18n;
use Rehike\ConfigManager\Config;
use Rehike\Model\Common\MButton;
use Rehike\Model\Dialog\MConfirmDialog;

/**
 * Data model for the shared sidebar for Rehike Panel pages (version/config).
 * 
 * @author The Rehike Maintainers
 */
class Sidebar
{
    /**
     * The child renderer object, which is handled by the template engine the
     * same as a creator sidebar renderer.
     */
    public object $creatorSidebarRenderer;

    /**
     * The ID of the currently selected tab.
     */
    private string $selectedTabId = "";

    public function __construct(string $selectedTabId = "")
    {
        $configStrings = i18n::getNamespace("rehike/config");
        $this->selectedTabId = $selectedTabId;
        $this->creatorSidebarRenderer = (object)[
            "sections" => []
        ];

        $sectionsResult = &$this->creatorSidebarRenderer->sections;
        $sectionsResult[] = (object)["creatorSidebarBranding" => (object)[
            "text" => $configStrings->get("title")
        ]];
        $sectionsResult = array_merge($sectionsResult, $this->buildConfigSection());
        $sectionsResult[] = (object)["creatorSidebarSeparatorRenderer" => (object)[]];
        $sectionsResult[] = $this->buildVersionSection();

        $buttonRendererTemp = [
            "class" => "rehike-creator-footer-button"
        ];
        $buttonRendererTemp = array_merge($buttonRendererTemp, self::getDisableRehikeButton());
        $sectionsResult[] = (object)["creatorSidebarButtonRenderer" => (object)$buttonRendererTemp];

        $this->selectCurrentItem();
    }

    protected function buildConfigSection(): array
    {
        $configStrings = i18n::getNamespace("rehike/config");
        
        // $nepetaEnabled = NepetaApi::isNepetaEnabled();
        
        return [
            $this->createSidebarSectionLink(
                id: "appearance",
                title: $configStrings->get("tabs.appearance"),
                icon: "rehike-appearance",
                href: "/rehike/config/appearance"
            ),
            $this->createSidebarSectionLink(
                id: "experiments",
                title: $configStrings->get("tabs.experiments"),
                icon: "rehike-experiments",
                href: "/rehike/config/experiments"
            ),
            // ...($nepetaEnabled ? [
            //     $this->createSidebarSectionLink(
            //         id: "extensions",
            //         title: "Nepeta",
            //         icon: "rehike-nepeta",
            //         href: "/rehike/extensions"
            //     )
            // ] : []),
            $this->createSidebarSectionLink(
                id: "advanced",
                title: $configStrings->get("tabs.advanced"),
                icon: "rehike-advanced",
                href: "/rehike/config/advanced"
            )
        ];
    }

    protected function buildVersionSection()
    {
        $versionStrings = i18n::getNamespace("rehike/version");
        
        return $this->createSidebarSectionLink(
            id: "rehike-version",
            title: $versionStrings->get("aboutRehike"),
            icon: "rehike-version",
            href: "/rehike/version"
        );
    }

    protected function createSidebarSectionLink(
            string $id,
            string $title,
            string $icon,
            string $href,
            array $items = []
    ): object
    {
        return (object) [
            "creatorSidebarSectionRenderer" => (object) [
                "sectionLink" => (object)[
                    "title" => (object) [
                        "simpleText" => $title
                    ],
                    "href" => $href,
                    "icon" => $icon
                ],
                "targetId" => $id,
                "isSelected" => false,
                "items" => $items
            ]
        ];
    }

    protected function createSidebarItem(string $id, string $title, string $href): object
    {
        return (object) [
            "creatorSidebarItemRenderer" => (object) [
                "title" => (object) [
                    "simpleText" => $title
                ],
                "targetId" => $id,
                "navigationEndpoint" => NavigationEndpoint::createEndpoint($href),
                "isSelected" => false
            ]
        ];
    }

    protected function selectCurrentItem(): void
    {
        foreach ($this->creatorSidebarRenderer->sections as $section)
        {
            $section = @$section->creatorSidebarSectionRenderer;

            if (@$section->targetId == $this->selectedTabId)
            {
                $section->isSelected = true;
            }
            else if (is_array(@$section->items))
            {
                foreach ($section->items as $item)
                {
                    $item = @$item->creatorSidebarItemRenderer;

                    if (@$item->targetId == $this->selectedTabId)
                    {
                        $item->isSelected = true;
                        $section->isSelected = true;
                    }
                }
            }
        }
    }

    private static function getDisableRehikeButton(): array
    {
        $i18n = i18n::getNamespace("rehike/disable_rehike");
        $isDisabled = Config::getConfigProp("hidden.disableRehike");

        $buttonText = $isDisabled
            ? $i18n->get("rhSettingsEnableRehike")
            : $i18n->get("disableRehike");

        return [
            "buttonRenderer" => new MButton([
                "style" => "STYLE_DARK",
                "class" => [ "rehike-config-disable-rehike-button" ],
                "attributes" => [
                    "disable-rehike-action" => $isDisabled ? "enable" : "disable"
                ],
                "text" => (object)[
                    "simpleText" => $buttonText
                ]
            ]),
            "rehikeDialogRenderer" => new MConfirmDialog(
                jsWrapperClassName: "rehike-dialog",
                title: $i18n->get("disableRehikeInfoHeader"),
                description: $i18n->get("disableRehikeInfoDescription"),
                cancelButton: new MButton([
                    "class" => [ "cancel-button" ],
                    "text" => (object)[
                        "simpleText" => $i18n->get("disableRehikeInfoCancel")
                    ]
                ]),
                confirmButton: new MButton([
                    "class" => [ "confirm-button" ],
                    "style" => "primary",
                    "text" => (object)[
                        "simpleText" => $i18n->get("disableRehikeInfoDisable")
                    ]
                ])
            )
        ];
    }
}