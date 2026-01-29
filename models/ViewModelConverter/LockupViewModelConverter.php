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
    public const STYLE_GRID    = 0;
    public const STYLE_LIST    = 1;
    public const STYLE_COMPACT = 2;
    
    private int $style = self::STYLE_GRID;
    
    public function getStyle(): int
    {
        return $this->style;
    }
    
    public function setStyle(int $newStyle): self
    {
        $this->style = $newStyle;
        return $this;
    }
    
    public function bakeClassicRenderer(): object
    {
        $rootPropName = $this->determineClassicPropertyName(
            $this->viewModel->contentType,
            $this->style
        );
        
        $resultObj = (object)[];

        switch ($this->viewModel->contentType)
        {
            case "LOCKUP_CONTENT_TYPE_PLAYLIST":
            case "LOCKUP_CONTENT_TYPE_ALBUM":
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
                self::STYLE_GRID    => "gridPlaylistRenderer",
                self::STYLE_LIST    => "playlistRenderer",
                self::STYLE_COMPACT => "compactPlaylistRenderer",
            },

            "LOCKUP_CONTENT_TYPE_ALBUM" => match ($direction)
            {
                self::STYLE_GRID    => "gridPlaylistRenderer",
                self::STYLE_LIST    => "playlistRenderer",
                self::STYLE_COMPACT => "compactPlaylistRenderer",
            },

            "LOCKUP_CONTENT_TYPE_VIDEO" => match ($direction)
            {
                self::STYLE_GRID    => "gridVideoRenderer",
                self::STYLE_LIST    => "videoRenderer",
                self::STYLE_COMPACT => "compactVideoRenderer",
            },
            
            default => "videoRenderer",
        };
    }
}