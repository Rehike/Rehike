<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * Converts InnerTube button view models to renderers.
 * 
 * @author The Rehike Maintainers
 */
class ButtonViewModelConverter extends BasicVMC
{
    public function bakeButtonRenderer(array $context = []): object
    {
        $out = [];

        if (isset($this->viewModel->trackingParams))
        {
            // just in case...
            $out["trackingParams"] = $this->viewModel->trackingParams;
        }

        if (isset($this->viewModel->accessibilityText))
        {
            $out["accessibility"] = (object)[
                "label" => $this->viewModel->accessibilityText
            ];

            $out["accessibilityData"] = (object)[
                "accessibilityData" => (object)[
                    "label" => $this->viewModel->accessibilityText
                ]
            ];
        }

        if (isset($this->viewModel->buttonSize))
        {
            $out["size"] = (object)[
                "sizeType" => str_replace("BUTTON_VIEW_MODEL_", "", $this->viewModel->buttonSize)
            ];
        }

        if (isset($this->viewModel->style))
        {
            $out["size"] = (object)[
                "sizeType" => str_replace("BUTTON_VIEW_MODEL_", "", $this->viewModel->style)
            ];
        }

        if (isset($this->viewModel->title))
        {
            $out["text"] = (object)[
                "text" => (object)[
                    "simpleText" => $this->viewModel->title
                ]
            ];
        }

        if (isset($this->viewModel->tooltip))
        {
            $out["tooltip"] = $this->viewModel->tooltip;
        }

        if (isset($this->viewModel->onTap))
        {
            // Commands are complicated.
            if (isset($this->viewModel->onTap->serialCommand))
            {
                $serialCommand = $this->viewModel->onTap->serialCommand;
                $finalCommands = [];

                foreach ($serialCommand->commands as $command)
                {
                    if (isset($command->innertubeCommand))
                    {
                        $finalCommands[] = $command->innertubeCommand;
                    }
                }

                if (count($finalCommands) == 1)
                {
                    $commandType = Utils::detectInnertubeCommandType($finalCommands[0]);
                    $out[$commandType] = $finalCommands[0];
                }
                else
                {
                    $finalCommands = array_map(
                        fn($cmd) => (object)[
                            Utils::detectInnertubeCommandType($cmd) => $cmd
                        ],
                        $finalCommands
                    );

                    $out["serialCommand"] = (object)[
                        "commands" => $finalCommands
                    ];
                }
            }
        }

        if (isset($context["targetId"]))
        {
            $out["targetId"] = $context["targetId"];
        }

        if (isset($context["isDisabled"]))
        {
            $out["isDisabled"] = $context["isDisabled"];
        }

        return (object)$out;
    }
}