<?php
namespace Rehike\Model\AddTo;

use Rehike\i18n\i18n;

/**
 * Model for the create playlist page of the add to playlist menu.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 * @author The Rehike Maintainers
 */
class MCreatePlaylist
{
    public string $label;
    public string $publicText;
    public string $unlistedText;
    public string $privateText;
    public string $playlistPrivacyText;
    public bool $isCompact = false;

    public function __construct(bool $compact = false)
    {
        $strs = i18n::getNamespace("addto");

        $this->label = $strs->get("playlistTitle");
        $this->publicText = $strs->get("privacyPublic");
        $this->unlistedText = $strs->get("privacyUnlisted");
        $this->privateText = $strs->get("privacyPrivate");
        $this->playlistPrivacyText = $strs->get("playlistPrivacy");

        $this->isCompact = $compact;
    }
}