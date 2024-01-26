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
        $playlistId = $data->playlistId;
        $isAlbum = str_starts_with($playlistId, "OL");

        // Title
        $this->title = ParsingUtils::getText($data->title);
        
        // Thumbnail
        $thumbnail = $data->playlistHeaderBanner->heroPlaylistThumbnailRenderer;
        if (isset($thumbnail->thumbnail))
        {
            // If the playlist is an album, then the sqp parameter is needed.
            // This results in the album thumbnail looking weird unfortunately.
            $this->thumbnail = new MPlaylistHeaderThumbnail(
                ParsingUtils::getThumb($thumbnail->thumbnail, 126, !$isAlbum),
                ParsingUtils::getUrl($thumbnail->onTap),
                ParsingUtils::getText($thumbnail->thumbnailOverlays->thumbnailOverlayHoverTextRenderer->text),
                isset($data->ownerEndpoint) ? (ParsingUtils::getUrl($data->ownerEndpoint) . "/playlists") : ""
            );
        }


        // Details (author, views, etc.)
        $ownerText = ParsingUtils::getText(($data->ownerText ?? $data->subtitle));
        if ($isAlbum)
        {
            // If it's an album the author text will have " • Album" appended at the end of it,
            // so we delete that part. (This applies to all languages)
            // TODO: More reliable method to extract the "album" part out (perhaps using i18n)
            $ownerText = trim(explode("•", $ownerText)[0]);
        }

        $authorDetails = (object) [
            "simpleText" => $ownerText
        ];
        // The InnerTube response no longer includes the data for the URL 
        // of the artist of the album (if the playlist is one), and just renders it as text.
        if (isset($data->ownerEndpoint))
            $authorDetails->navigationEndpoint = $data->ownerEndpoint;
        
        $this->details[] = $authorDetails;


        foreach ($data->byline as $byline)
        {
            $this->details[] = (object) [
                "simpleText" => ParsingUtils::getText($byline->playlistBylineRenderer->text)
            ];
        }



        // Actions (play all, share, save)
        if (isset($data->playButton->buttonRenderer))
        {
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
}