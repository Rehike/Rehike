<?php
namespace Rehike\Model\AddTo;

use Rehike\i18n;

class MAddTo
{
    public $createNewText;
    public $searchText;
    public $playlists;
    public $createPlaylistPanelRenderer;

    public function __construct($lists)
    {
        $strs = i18n::getNamespace("addto");

        $this->createNewText = $strs->createNewPlaylist;
        $this->searchText = $strs->searchPlaylists;

        $this->createPlaylistPanelRenderer = new MCreatePlaylist(true);
        
        $this->playlists = $lists;
    }
}