<?php
namespace Rehike\Model\AddTo;

use Rehike\i18n\i18n;

class MCreatePlaylist
{
    public $label;
    public $publicText;
    public $unlistedText;
    public $privateText;
    public $playlistPrivacyText;
    public $isCompact = false;

    public function __construct($compact = false)
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