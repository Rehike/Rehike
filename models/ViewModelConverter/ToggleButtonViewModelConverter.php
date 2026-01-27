<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * Converts toggle button view models to renderers.
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
            $defaultVm = $vm->defaultButtonViewModel->buttonViewModel;
            $converter = new ButtonViewModelConverter(
                $defaultVm,
                $this->frameworkUpdates
            );
            $defaultButton = $converter->bakeButtonRenderer($sharedButtonContext);
        }

        if (isset($vm->toggledButtonViewModel->buttonViewModel))
        {
            $toggledVm = $vm->toggledButtonViewModel->buttonViewModel;
            $converter = new ButtonViewModelConverter(
                $toggledVm,
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
        
        if (isset($defaultVm->iconName))
        {
            $out["defaultIcon"] = (object)[
                "iconType" => $defaultVm->iconName,
            ];
        }
        if (isset($toggledVm->iconName))
        {
            $out["toggledIcon"] = (object)[
                "iconType" => $defaultVm->iconName,
            ];
        }

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