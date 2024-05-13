<?php
namespace Rehike\Model\AddTo;

use Rehike\i18n\i18n;

/**
 * Model for the add to playlist menu.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MAddTo
{
    public string $createNewText;
    public string $searchText;
    
    /**
     * An array of all playlist items (from InnerTube) in the menu.
     */
    public array $playlists;
    
    /**
     * The "create new playlist" panel renderer.
     */
    public ?MCreatePlaylist $createPlaylistPanelRenderer = null;

    public function __construct(array $lists)
    {
        $strs = i18n::getNamespace("addto");

        $this->createNewText = $strs->get("createNewPlaylist");
        $this->searchText = $strs->get("searchPlaylists");

        $this->createPlaylistPanelRenderer = new MCreatePlaylist(true);
        
        $this->playlists = $lists;
    }
}