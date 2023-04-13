<?php
namespace Rehike\Model\Playlist;

use Rehike\Util\ParsingUtils;
use Rehike\Model\Common\MButton;

class MPlaylistHeader
{
    public string $title;

    public MPlaylistHeaderThumbnail $thumbnail;

    /** @var object[] */
    public array $details = [];

    /** @var MButton[] */
    public array $actions = [];

    public function __construct(object $data)
    {
        // Title
        $this->title = ParsingUtils::getText($data->title);
        
        // Thumbnail
        $thumbnail = $data->playlistHeaderBanner->heroPlaylistThumbnailRenderer;
        $this->thumbnail = new MPlaylistHeaderThumbnail(
            ParsingUtils::getThumb($thumbnail->thumbnail, 126),
            ParsingUtils::getUrl($thumbnail->onTap),
            ParsingUtils::getText($thumbnail->thumbnailOverlays->thumbnailOverlayHoverTextRenderer->text),
            ParsingUtils::getUrl($data->ownerEndpoint) . "/playlists"
        );

        // Details (author, views, etc.)
        $this->details[] = (object) [
            "simpleText" => ParsingUtils::getText($data->ownerText),
            "navigationEndpoint" => $data->ownerEndpoint
        ];
        foreach ($data->byline as $byline)
        {
            $this->details[] = (object) [
                "simpleText" => ParsingUtils::getText($byline->playlistBylineRenderer->text)
            ];
        }

        // Actions (play all, share, save)
        $this->actions[] = new MButton([
            "text" => $data->playButton->buttonRenderer->text,
            "navigationEndpoint" => $data->playButton->buttonRenderer->navigationEndpoint,
            "icon" => true,
            "class" => [
                "playlist-play-all",
                "play-all-icon-btn"
            ]
        ]);
    }
}