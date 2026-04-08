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
        // XXX(isabella): For some reason, it is possible in rare circumstances for a video
        // renderer to lack this property. Without research, this seems to occur on TV-like
        // YPC videos like https://www.youtube.com/watch?v=waYebPQ-pNk&pp=sAQB0gcJCdkKAYcqIYzv
        // so I will assume it's just this content type which is affected. For now, it seems
        // safe to assume that anything lacking a content type property can be assumed to be
        // a video.
        $contentType = $this->viewModel->contentType ?? "LOCKUP_CONTENT_TYPE_VIDEO";
        
        $rootPropName = $this->determineClassicPropertyName(
            $contentType,
            $this->style
        );
        
        $resultObj = (object)[];

        switch ($contentType)
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