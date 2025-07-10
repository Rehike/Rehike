<?php
namespace Rehike\Model\ViewModelConverter;

/**
 * Converts a lockup view model to the classic format that Rehike expects.
 * 
 * @author Isabella Lulamoon <kawapure@gmail.com>
 * @author The Rehike Maintainers
 */
class LockupViewModelConverter extends BasicVMC
{
    public const DIRECTION_GRID = 0;
    public const DIRECTION_LIST = 1;
    
    private int $direction = self::DIRECTION_GRID;
    
    public function getDirection(): int
    {
        return $this->direction;
    }
    
    public function setDirection(int $newDirection): self
    {
        $this->direction = $newDirection;
        return $this;
    }
    
    public function bakeClassicRenderer(): object
    {
        $rootPropName = $this->determineClassicPropertyName(
            $this->viewModel->contentType,
            $this->direction
        );
        
        $resultObj = (object)[];

        switch ($this->viewModel->contentType)
        {
            case "LOCKUP_CONTENT_TYPE_PLAYLIST":
            {
                $playlistConv = new PlaylistRendererViewModelConverter($this->viewModel, $this->frameworkUpdates);
                $resultObj = $playlistConv->bake($this);
                break;
            }
            case "LOCKUP_CONTENT_TYPE_VIDEO":
            {
                \Rehike\YtApp::getInstance()->hasEvilWatchSidebarExperimentFromHell = true;
                $videoConv = new VideoRendererViewModelConverter($this->viewModel, $this->frameworkUpdates);
                $resultObj = $videoConv->bake($this);
                break;
            }
        }
        
        return (object)[
            $rootPropName => $resultObj
        ];
    }
    
    private function determineClassicPropertyName(string $lockupType, int $direction): string
    {
        return match ($lockupType)
        {
            "LOCKUP_CONTENT_TYPE_PLAYLIST" => match ($direction)
            {
                self::DIRECTION_GRID => "gridPlaylistRenderer",
                self::DIRECTION_LIST => "playlistRenderer",
            },

            "LOCKUP_CONTENT_TYPE_VIDEO" => match ($direction)
            {
                self::DIRECTION_GRID => "gridVideoRenderer",
                self::DIRECTION_LIST => "videoRenderer",
            },
            
            default => "videoRenderer",
        };
    }
}