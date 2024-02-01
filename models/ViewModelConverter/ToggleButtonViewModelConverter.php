<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * intro
 * 
 * Toggle button renderers are structured very differently from the view models.
 * The view models (inefficiently) store two separate button models, whereas the
 * renderers extend from the button models and add additional fields for their
 * respective states.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class ToggleButtonViewModelConverter extends BasicVMC
{
    public function bakeToggleButtonRenderer(array $context = []): object
    {
        $vm = $this->viewModel;
        $out = [];

        $sharedButtonContext = [
            "isDisabled" => $vm->isTogglingDisabled ?? false
        ];

        if (isset($vm->defaultButtonViewModel->buttonViewModel))
        {
            $converter = new ButtonViewModelConverter(
                $vm->defaultButtonViewModel->buttonViewModel,
                $this->frameworkUpdates
            );
            $defaultButton = $converter->bakeButtonRenderer($sharedButtonContext);
        }

        if (isset($vm->toggledButtonViewModel->buttonViewModel))
        {
            $converter = new ButtonViewModelConverter(
                $vm->toggledButtonViewModel->buttonViewModel,
                $this->frameworkUpdates
            );
            $toggledButton = $converter->bakeButtonRenderer($sharedButtonContext);
        }

        // Property maps:
        $propertyMaps = [
            "defaultButton" => [
                // These should be the same between the two:
                "accessibility" => "accessibility",
                "isDisabled" => "isDisabled",
                "trackingParams" => "trackingParams",

                "style" => "style",
                "size" => "size",
                "accessibilityData" => "accessibilityData",
                "tooltip" => "defaultTooltip",
                "icon" => "defaultIcon",
                "navigationEndpoint" => "defaultNavigationEndpoint",
                "serviceEndpoint" => "defaultServiceEndpoint",
            ],
            "toggledButton" => [
                "style" => "toggledStyle",
                "size" => "toggledSize",
                "accessibilityData" => "toggledAccessibilityData",
                "tooltip" => "toggledTooltip",
                "icon" => "toggledIcon",
                "navigationEndpoint" => "toggledNavigationEndpoint",
                "serviceEndpoint" => "toggledServiceEndpoint"
            ]
        ];

        foreach ($propertyMaps as $var => $defs)
        {
            foreach ($defs as $origName => $destName)
            {
                // PHP is so evil and I love it.
                if (isset($$var->{$origName}))
                {
                    $out[$destName] = $$var->{$origName};
                }
            }
        }

        if (isset($vm->identifier))
        {
            $out["targetId"] = $vm->identifier;
        }

        $out["isToggled"] = $context["isToggled"] ?? false;

        return (object)$out;
    }
}