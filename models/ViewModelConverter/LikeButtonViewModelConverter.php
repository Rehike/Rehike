<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\ViewModelParser;

/**
 * Converts the like button view model to a flatter model like the previous
 * InnerTube implementation.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class LikeButtonViewModelConverter extends BasicVMC
{
    public function bakeSegmentedLikeDislikeButtonRenderer(array $context = []): object
    {
        $out = [];
        $vm = $this->viewModel;

        $likeStatus = "INDIFFERENT";
        // I have no idea why it's nested like this with an empty parent.
        if (isset($vm->likeButtonViewModel->likeButtonViewModel))
        {
            $root = $vm->likeButtonViewModel->likeButtonViewModel;

            // Detect the like status. We only do this on the like button, since
            // it should always be there, and because it has better data than the
            // dislike button.
            if (isset($root->likeStatusEntity))
            {
                $likeStatusEntity = $root->likeStatusEntity;
            }
            else
            {
                $parser = new ViewModelParser($root, $this->frameworkUpdates);
                $likeStatusEntity = $parser->getViewModelEntities([
                    "likeStatusEntityKey" => "likeStatusEntity"
                ])["likeStatusEntity"];
            }
            $likeStatus = $likeStatusEntity->likeStatus;
            
            $tbConverter = new ToggleButtonViewModelConverter(
                $root->toggleButtonViewModel->toggleButtonViewModel,
                $this->frameworkUpdates
            );
            $toggleButton = $tbConverter->bakeToggleButtonRenderer([
                "isToggled" => $likeStatus == "LIKE"
            ]);
            $likeButton = (object)[
                "toggleButtonRenderer" => $toggleButton
            ];
        }

        if (isset($vm->dislikeButtonViewModel->dislikeButtonViewModel))
        {
            $root = $vm->dislikeButtonViewModel->dislikeButtonViewModel;
            
            $tbConverter = new ToggleButtonViewModelConverter(
                $root->toggleButtonViewModel->toggleButtonViewModel,
                $this->frameworkUpdates
            );
            $toggleButton = $tbConverter->bakeToggleButtonRenderer([
                "isToggled" => $likeStatus == "DISLIKE"
            ]);
            $dislikeButton = (object)[
                "toggleButtonRenderer" => $toggleButton
            ];
        }

        $out["likeButton"] = $likeButton;
        $out["dislikeButton"] = $dislikeButton;

        return (object)$out;
    }
}