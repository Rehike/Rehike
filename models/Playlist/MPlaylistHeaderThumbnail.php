<?php
namespace Rehike\Model\Playlist;

class MPlaylistHeaderThumbnail
{
    public function __construct(
        public string $thumbnail,
        public string $url,
        public string $playAllText,
        public string $allPlaylistsUrl
    ) {}
}