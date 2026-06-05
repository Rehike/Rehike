<?php
namespace Rehike\Model\ViewModelConverter;

use Rehike\Util\ParsingUtils;

/**
 * Converts a playlist lockup view model to the classic format that Rehike expects.
 * 
 * This is for use on playlist pages, and not used elsewhere. Lockups that direct to playlist
 * pages use {@see PlaylistRendererViewModelConverter}.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class PlaylistVideoRendererViewModelConverter extends BasicVMC
{
    public function bake(): object
    {
        $videoRendererVmc = new VideoRendererViewModelConverter(
            $this->viewModel,
            $this->frameworkUpdates
        );
        $result = $videoRendererVmc->bake(null);
        
        if ($timeOverlay = ParsingUtils::getThumbnailOverlay(
            $result, "thumbnailOverlayTimeStatusRenderer"))
        {
            $result->lengthText = $timeOverlay->text;
        }
        
        return $result;
    }
}