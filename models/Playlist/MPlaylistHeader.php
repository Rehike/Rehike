<?php
namespace Rehike\Model\Playlist;

use Rehike\Util\ParsingUtils;
use Rehike\i18n\i18n;
use Rehike\Model\Common\MButton;
use Rehike\Model\Clickcard\MSigninClickcard;
use Rehike\Signin\API as SignIn;

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
        $i18n = i18n::getNamespace("playlist");

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
                    "play-all-icon-btn",
                    "yt-uix-button-playlist-action"
                ]
            ]);
        }
        if (isset($data->shareButton->buttonRenderer))
        {
            $this->actions[] = new MButton([
                "text" => (object) [
                    "simpleText" => $data->shareButton->buttonRenderer->tooltip
                ],
                "tooltip" => $i18n->get("shareTooltip"),
                "icon" => true,
                "class" => [
                    "playlist-share",
                    "yt-uix-button-playlist-action"
                ],
                "attributes" => [
                    "button-toggle" => "true"
                ],
                "clickcard" => new MPlaylistShareClickcard()
            ]);
        }
        if (isset($data->saveButton->toggleButtonRenderer))
        {
            // TODO: fix toggle for save button
            $likeClass = $data->saveButton->toggleButtonRenderer->isToggled ? "yt-uix-playlistlike-liked" : "yt-uix-playlistlike";
            $watchI18N = i18n::getNamespace("watch");
            $saveButtonData = [
                "text" => (object) [
                    "simpleText" => $i18n->get("saveText")
                ],
                "tooltip" => $i18n->get("saveTooltip"),
                "icon" => true,
                "id" => "gh-playlist-save",
                "class" => [
                    $likeClass,
                    "watch-playlist-like"
                ],
                "attributes" => [
                    "token" => "QUFFLUhqbmNvOU9NYTRnUkh6NmVtSjJLbUI0QmVRMnllUXxBQ3Jtc0tscm1hckl4LUxhWTE3cHUyZGh3NDdRWFFqMUNFOGZ1ZEdlYnNYTEhuYmJscnVTVTdpR2x6N2RmeDBMbGI5U1hUOHltTnI4TlB3SVpwNTNyOHlpQlJiRDc4R0R3Q1pDcWZpUEpiNTdHR1lzM2N4QUliUWZFRThpR29ZVVhRdkhqOXJaTnBaSHlCazM3Z0dHOW1ycnlaNlc2aHF2c3c=",
                    "toggle-class" => "yt-uix-button-toggled",
                    "like-label" => $i18n->get("saveText"),
                    "unlike-label" => $i18n->get("savedText"),
                    "like-tooltip" => $i18n->get("saveTooltip"),
                    "unlike-tooltip" => $watchI18N->get("playlistUnsave"),
                    "playlist-id" => $playlistId
                ]
            ];

            if (!SignIn::isSignedIn())
            {
                $saveButtonData["clickcard"] = new MSigninClickcard(
                    $watchI18N->get("clickcardPlaylistSignIn"),
                    "",
                    [
                        "text" => $watchI18N->get("clickcardSignIn"),
                        "href" => "https://accounts.google.com/ServiceLogin?continue=https%3A%2F%2Fwww.youtube.com%2Fsignin%3Fnext%3D%252F%253Faction_handle_signin%3Dtrue%26feature%3D__FEATURE__%26hl%3Den%26app%3Ddesktop&passive=true&hl=en&uilel=3&service=youtube"
                    ]
                );
            }

            $this->actions[] = new MButton($saveButtonData);
        }
    }
}